<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* UserCities Model
*
* @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
* @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
* @property \App\Model\Table\UserCityDevicesTable|\Cake\ORM\Association\HasMany $UserCityDevices
*
* @method \App\Model\Entity\UserCity get($primaryKey, $options = [])
* @method \App\Model\Entity\UserCity newEntity($data = null, array $options = [])
* @method \App\Model\Entity\UserCity[] newEntities(array $data, array $options = [])
* @method \App\Model\Entity\UserCity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
* @method \App\Model\Entity\UserCity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
* @method \App\Model\Entity\UserCity[] patchEntities($entities, array $data, array $options = [])
* @method \App\Model\Entity\UserCity findOrCreate($search, callable $callback = null, $options = [])
*
* @mixin \Cake\ORM\Behavior\TimestampBehavior
*/
class UserCitiesTable extends Table
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

    $this->setTable('user_cities');

    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');
    $this->addBehavior('Log');

    $this->addBehavior('CounterCache', [
      'Users' => ['citizen_count' => ['conditions' => ['UserCities.deleted IS ' => null]]],
      'Cities' => [
        'citizen_validated_count' => [
          'conditions' => ['UserCities.validated' => 1, 'UserCities.refused' => 0, 'UserCities.deleted IS' => null]
        ],
        'citizen_unvalidated_count' => [
          'finder' => 'unvalidated'
        ],
        'citizen_refused_count' => [
          'conditions' => ['UserCities.refused' => 1, 'UserCities.deleted IS' => null]
        ],
        'citizen_bloqued_count' => [
          'conditions' => ['UserCities.exclusion_expiration IS NOT ' => null, 'UserCities.validated' => 1, 'UserCities.refused' => 0, 'UserCities.deleted IS' => null]
        ]
      ]
    ]);

    $this->belongsTo('Cities', [
      'foreignKey' => 'city_id',

    ]);
    $this->belongsTo('Users', [
      'foreignKey' => 'user_id',

    ]);

    $this->hasMany('UserCityDevices');

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
    ->boolean('validated')
    ->allowEmpty('validated');

    $validator
    ->boolean('refused')
    ->allowEmpty('refused');

    $validator
    ->dateTime('deleted')
    ->allowEmpty('deleted');

    //        $validator
    //            ->dateTime('logged')
    //            ->allowEmpty('logged');

    $validator
    ->boolean('current')
    ->allowEmpty('current');

    $validator
    ->boolean('cron_in_progress')
    ->allowEmpty('cron_in_progress');

    $validator
    ->boolean('notified')
    ->allowEmpty('notified');

    $validator
    ->scalar('presentation')
    ->lengthBetween('presentation', [2, 10000], 'Le champ doit contenir entre 2 et 10000 caractères')
    ->allowEmpty('presentation');

    $validator
    ->boolean('exclusion_discussion_message')
    ->allowEmpty('exclusion_discussion_message');

    $validator
    ->boolean('exclusion_publication_comment')
    ->allowEmpty('exclusion_publication_comment');

    $validator
    ->boolean('exclusion_newsgroup_comment')
    ->allowEmpty('exclusion_newsgroup_comment');

    $validator
    ->boolean('exclusion_suggestion_comment')
    ->allowEmpty('exclusion_suggestion_comment');

    $validator
    ->boolean('exclusion_classified_ad')
    ->allowEmpty('exclusion_classified_ad');

    $validator
    ->date('exclusion_expiration', "ymd", 'Le champs doit être une date')
    ->notEmpty('exclusion_expiration');


    $validator
    ->integer('notification_newsgroup')
    ->range('notification_newsgroup', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_newsgroup');

    $validator
    ->integer('notification_newsgroup_comment')
    ->range('notification_newsgroup_comment', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_newsgroup_comment');

    $validator
    ->integer('notification_publication')
    ->range('notification_publication', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_publication');

    $validator
    ->integer('notification_publication_comment')
    ->range('notification_publication_comment', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_publication_comment');

    $validator
    ->integer('notification_poll')
    ->range('notification_poll', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_poll');

    $validator
    ->integer('notification_alert')
    ->range('notification_alert', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_alert');

    $validator
    ->integer('notification_mayor')
    ->range('notification_mayor', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_mayor');

    $validator
    ->integer('notification_suggestion')
    ->range('notification_suggestion', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_suggestion');

    $validator
    ->integer('notification_suggestion_comment')
    ->range('notification_suggestion_comment', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_suggestion_comment');

    $validator
    ->integer('notification_discussion_message')
    ->range('notification_discussion_message', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_discussion_message');

    $validator
    ->integer('notification_publication_frequency')
    ->range('notification_publication_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_publication_frequency');

    $validator
    ->integer('notification_publication_comment_frequency')
    ->range('notification_publication_comment_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_publication_comment_frequency');

    $validator
    ->integer('notification_suggestion_frequency')
    ->range('notification_suggestion_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_suggestion_frequency');

    $validator
    ->integer('notification_suggestion_comment_frequency')
    ->range('notification_suggestion_comment_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_suggestion_comment_frequency');

    $validator
    ->integer('notification_newsgroup_frequency')
    ->range('notification_newsgroup_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_newsgroup_frequency');

    $validator
    ->integer('notification_newsgroup_comment_frequency')
    ->range('notification_newsgroup_comment_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_newsgroup_comment_frequency');

    $validator
    ->integer('notification_poll_frequency')
    ->range('notification_poll_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_poll_frequency');

    $validator
    ->integer('notification_alert_frequency')
    ->range('notification_alert_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_alert_frequency');

    $validator
    ->integer('notification_discussion_message_frequency')
    ->range('notification_discussion_message_frequency', [1, 3], "Veuillez vérifier le champs")
    ->notEmpty('notification_discussion_message_frequency');

    $validator
    ->scalar('street_number')
    ->maxLength('street_number', 10)
    ->allowEmpty('street_number');

    $validator
    ->scalar('route')
    ->maxLength('route', 155)
    ->allowEmpty('route');

    $validator
    ->scalar('postal_code')
    ->maxLength('postal_code', 10)
    ->add('postal_code', 'postalFormat', [
      'rule' => ['custom', '/^[0-9]{5}$/i'],
      'message' => 'Le code postal n\'est pas en bon format'
    ])
    ->range('postal_code', ['01000', '98000'], 'Ce champ doit être un code postal valide')
    ->allowEmpty('postal_code');

    $validator
    ->scalar('locality')
    ->maxLength('locality', 155)
    ->allowEmpty('locality');

    $validator
    ->scalar('country')
    ->maxLength('country', 155)
    ->allowEmpty('country');

    $validator
    ->scalar('country_short')
    ->maxLength('country_short', 10)
    ->allowEmpty('country_short');

    $validator
    ->decimal('lat')
    ->allowEmpty('lat');

    $validator
    ->decimal('lng')
    ->allowEmpty('lng');

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



  public function findUnvalidated(\Cake\ORM\Query $query, array $options){
    $query->contain(['Users'])->where(['UserCities.validated' => 0, 'UserCities.refused' => 0, 'UserCities.deleted IS' => null, 'Users.registered' => 1, 'Users.deleted IS ' => null]);
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

      $user_city = $this->find()->contain(['Users'])->where(['UserCities.id' => $entity->id])->first();
    if ($user_city) {
      $city = $this->Cities->find()->where(['id' => $user_city->city_id])->first();
      if ($city) {
        (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-update', ['city' => $city]);
      }

      if ($entity->isNew()) {
        (new Socket())->emit('/dynamic-' . $entity->city_id, 'user-city-create', ['user_city' => $user_city]);
      } else {
        (new Socket())->emit('/dynamic-' . $entity->city_id, 'user-city-update', ['user_city' => $user_city]);
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
      (new Socket())->emit('/dynamic-' . $entity->city_id, 'user-city-delete', ['user_city' => $entity]);
  }


}
