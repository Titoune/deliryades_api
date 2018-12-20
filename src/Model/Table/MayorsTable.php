<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Mayors Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\MandatariesTable|\Cake\ORM\Association\HasMany $Mandataries
 * @method \App\Model\Entity\Mayor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Mayor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Mayor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mayor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mayor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Mayor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mayor findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class MayorsTable extends Table
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

        $this->setTable('mayors');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');


        $this->addBehavior('CounterCache', [
            'Users' => ['mayor_count' => ['conditions' => ['Mayors.deleted IS' => null]]]

        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);


        $this->hasMany('Mandataries', [
            'foreignKey' => 'mayor_id'
        ]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);

        $this->hasMany('MayorDevices');
        $this->hasMany('Notifications');


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
            ->boolean('activated')
            ->notEmpty('activated');

        $validator
            ->dateTime('logged')
            ->allowEmpty('logged');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->integer('since')
            ->notEmpty('since', 'Ce champ est requis')
            ->range('since', [(date('Y') - 100), date('Y')], 'La valeur du champ est incorrecte');

        $validator
            ->scalar('slogan')
            ->lengthBetween('slogan', [2, 209], 'Le champ doit contenir entre 2 et 209 caractères')
            ->allowEmpty('slogan');

        $validator
            ->scalar('presentation')
            ->lengthBetween('presentation', [2, 10000], 'Le champ doit contenir entre 2 et 10000 caractères')
            ->allowEmpty('presentation');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->allowEmpty('slug');


        $validator
            ->integer('notification_user_city')
            ->range('notification_user_city', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_user_city');

        $validator
            ->integer('notification_publication_comment')
            ->range('notification_publication_comment', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_publication_comment');

        $validator
            ->integer('notification_suggestion')
            ->range('notification_suggestion', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_suggestion');

        $validator
            ->integer('notification_suggestion_comment')
            ->range('notification_suggestion_comment', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_suggestion_comment');

        $validator
            ->integer('notification_newsgroup_comment')
            ->range('notification_newsgroup_comment', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_newsgroup_comment');

        $validator
            ->integer('notification_discussion_message')
            ->range('notification_discussion_message', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_discussion_message');

        $validator
            ->integer('notification_user_city_frequency')
            ->range('notification_user_city_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_user_city_frequency');

        $validator
            ->integer('notification_publication_comment_frequency')
            ->range('notification_publication_comment_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_publication_comment_frequency');

        $validator
            ->integer('notification_suggestion_frequency')
            ->range('notification_suggestion_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_suggestion_frequency');

        $validator
            ->integer('notification_suggestion_comment_frequency')
            ->range('notification_suggestion_comment_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_suggestion_comment_frequency');

        $validator
            ->integer('notification_newsgroup_comment_frequency')
            ->range('notification_newsgroup_comment_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_newsgroup_comment_frequency');

        $validator
            ->integer('notification_discussion_message_frequency')
            ->range('notification_discussion_message_frequency', [1, 3], "Veuillez vérifier le champs")
            ->allowEmpty('notification_discussion_message_frequency');

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

        return $rules;
    }

    //finder personnalisés
    public function findUser(Query $query, array $options)
    {
        $query->contain(['Users']);
        return $query;
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

        $mayor = $this->find()->contain(['Users'])->where(['Mayors.id' => $entity->id])->first();
        if ($mayor) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'mayor-create', ['mayor' => $mayor]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'mayor-update', ['mayor' => $mayor]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'mayor-delete', ['mayor' => $entity]);
    }

}
