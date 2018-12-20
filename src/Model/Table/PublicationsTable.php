<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * Publications Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PublicationCommentsTable|\Cake\ORM\Association\HasMany $PublicationComments
 * @property \App\Model\Table\PublicationLikesTable|\Cake\ORM\Association\HasMany $PublicationLikes
 *
 * @method \App\Model\Entity\Publication get($primaryKey, $options = [])
 * @method \App\Model\Entity\Publication newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Publication[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Publication|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Publication patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Publication[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Publication findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class PublicationsTable extends Table
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

        $this->setTable('publications');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['publication_count']]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->hasMany('PublicationComments', [
            'foreignKey' => 'publication_id']);
        $this->hasMany('PublicationLikes', [
            'foreignKey' => 'publication_id',
        ]);

        $this->hasMany('Pictures', [
            'foreignKey' => 'foreign_id',
            'dependent' => true,
        ])->setConditions(['Pictures.model' => 'Publications']);

        $this->hasOne('PublicationDiffusions', [
            'foreignKey' => 'initial_publication_id',
            'bindingKey' => 'initial_publication_id'
        ]);

        $this->hasOne('MyPublicationDiffusions', [
            'foreignKey' => 'initial_publication_id',
            'className' => 'PublicationDiffusions'
        ]);
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
            ->allowEmpty('publication_type');

        $validator
            ->scalar('title')
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères');

        $validator
            ->scalar('content')
            ->allowEmpty('content')
            ->lengthBetween('content', [2, 2000], 'Le champ doit contenir entre 2 et 2000 caractères');

        $validator
            ->urlWithProtocol('website_url', 'Le champ doit être une url valide')
            ->allowEmpty('website_url');

        $validator
            ->scalar('website_title')
            ->maxLength('website_title', 255)
            ->allowEmpty('website_title');

        $validator
            ->scalar('website_picture')
            ->maxLength('website_picture', 255)
            ->allowEmpty('website_picture');

        $validator
            ->scalar('website_description')
            ->allowEmpty('website_description');


        $validator
            ->scalar('pdf')
            ->maxLength('pdf', 255)
            ->allowEmpty('pdf');


        $validator
            ->scalar('video')
            ->maxLength('video', 155)
            ->allowEmpty('video');

        $validator
            ->scalar('hosted_video')
            ->allowEmpty('hosted_video');


        $validator
            ->dateTime('published', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('published');

        $validator
            ->boolean('open_comment')
            ->allowEmpty('open_comment');


        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('notified')
            ->allowEmpty('notified');

        $validator
            ->boolean('share')
            ->allowEmpty('share');

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
        $rules->add($rules->existsIn(['city_id'], 'Cities'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
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

        $publication = $this->find()->contain(['Pictures' => ['sort' => ['Pictures.position' => 'asc']], 'PublicationDiffusions.Users.Cities'])->where(['Publications.id' => $entity->id])->first();
        if ($publication) {
            $publication->publication_comments = $this->PublicationComments->find()->contain(['Users'])->where(['PublicationComments.publication_id' => $publication->id, 'PublicationComments.deleted IS ' => null])->order(['PublicationComments.created ' => 'desc'])->limit(2);
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'publication-create', ['publication' => $publication]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'publication-update', ['publication' => $publication]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'publication-delete', ['publication' => $entity]);
    }


}
