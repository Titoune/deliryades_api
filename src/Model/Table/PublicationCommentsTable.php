<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PublicationComments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PublicationsTable|\Cake\ORM\Association\BelongsTo $Publications
 *
 * @method \App\Model\Entity\PublicationComment get($primaryKey, $options = [])
 * @method \App\Model\Entity\PublicationComment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PublicationComment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PublicationComment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PublicationComment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PublicationComment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PublicationComment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PublicationCommentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('publication_comments');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Publications' => [
                'comment_count' => ['conditions' => ['deleted IS' => null]],
                'participant_count' => [
                    'finder' => 'participants'
                ]
            ]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Publications', [
            'foreignKey' => 'publication_id',

        ]);

        $this->hasMany('Reports', [
            'dependent' => true,
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Reports.model' => 'publication-comment']);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('uniqid')
            ->maxLength('uniqid', 255)
            ->allowEmpty('uniqid');

        $validator
            ->scalar('content')
            ->notEmpty('content', 'Ce champ est requis')
            ->lengthBetween('content', [1, 5000], 'Le champ doit contenir entre 1 et 5000 caractères');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');


        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('notified')
            ->allowEmpty('notified');

        return $validator;
    }


    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['publication_id'], 'Publications'));

        return $rules;
    }

    public function findParticipants(Query $query, array $options)
    {
        return $query->where(['deleted IS ' => null])->distinct('user_id');
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->uniqid) {
            $entity->uniqid = Tools::_getRandomHash();
        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $publication_comment = $this->find()->contain(['Users', 'Publications'])->where(['PublicationComments.id' => $entity->id])->first();
        if ($publication_comment) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $publication_comment->publication->city_id, 'publication-comment-create', ['publication_comment' => $publication_comment]);
            } else {
                (new Socket())->emit('/dynamic-' . $publication_comment->publication->city_id, 'publication-comment-update', ['publication_comment' => $publication_comment]);
            }
        }
    }

    public function beforeDelete($event, $entity, $options)
    {
        if (isset($options['type']) && $options['type'] == 'soft') {
            $entity = $this->get($entity->id);
            $entity->deleted = date('Y-m-d H:i:s');
            $this->save($entity);
            $event->stopPropagation();
            $this->afterDelete($event, $entity, $options);
            return true;
        }
    }

    public function afterDelete($event, $entity, $options)
    {
        //(new Firestore())->delete($this->getTable(), $entity->uniqid);

        $publication = $this->Publications->find()->where(['Publications.id' => $entity->publication_id])->first();
        if ($publication) {
            (new Socket())->emit('/dynamic-' . $publication->city_id, 'publication-comment-delete', ['publication_comment' => $entity]);
        }
    }

}
