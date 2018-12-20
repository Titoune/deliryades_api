<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NewsgroupComments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\NewsgroupsTable|\Cake\ORM\Association\BelongsTo $Newsgroups
 *
 * @method \App\Model\Entity\NewsgroupComment get($primaryKey, $options = [])
 * @method \App\Model\Entity\NewsgroupComment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NewsgroupComment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NewsgroupComment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NewsgroupComment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NewsgroupComment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NewsgroupComment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NewsgroupCommentsTable extends Table
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

        $this->setTable('newsgroup_comments');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'Newsgroups' => [
                'comment_count' => ['conditions' => ['deleted IS ' => null]],
                'participant_count' => [
                    'finder' => 'participants'
                ]
            ]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Newsgroups', [
            'foreignKey' => 'newsgroup_id',

        ]);

        $this->hasMany('Reports', [
            'dependent' => true,
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Reports.model' => 'newsgroup-comment']);
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
            ->lengthBetween('content', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères');

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
        $rules->add($rules->existsIn(['newsgroup_id'], 'Newsgroups'));

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

        $newsgroup_comment = $this->find()->contain(['Users'])->where(['NewsgroupComments.id' => $entity->id])->first();
        if ($newsgroup_comment) {

            $this->hasOne('NewsgroupComments', [
                'className' => 'NewsgroupComments',
                'sort' => [
                    'created' => 'desc'
                ],
                'strategy' => 'select'
            ]);
            $newsgroup = $this->Newsgroups->find()->contain(['NewsgroupComments' => ['conditions' => ['NewsgroupComments.deleted IS ' => null]], 'NewsgroupComments.Users'])->where(['Newsgroups.id' => $entity->newsgroup_id])->first();
            if ($newsgroup) {
                (new Socket())->emit('/dynamic-' . $newsgroup->city_id, 'newsgroup-update', ['newsgroup' => $newsgroup]);
                if ($entity->isNew()) {
                    (new Socket())->emit('/dynamic-' . $newsgroup->city_id, 'newsgroup-comment-create', ['newsgroup_comment' => $newsgroup_comment]);
                } else {
                    (new Socket())->emit('/dynamic-' . $newsgroup->city_id, 'newsgroup-comment-update', ['newsgroup_comment' => $newsgroup_comment]);
                }
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

        $newsgroup = $this->Newsgroups->find()->where(['Newsgroups.id' => $entity->newsgroup_id])->first();
        if ($newsgroup) {
            (new Socket())->emit('/dynamic-' . $newsgroup->city_id, 'newsgroup-comment-delete', ['newsgroup_comment' => $entity]);
        }
    }


}
