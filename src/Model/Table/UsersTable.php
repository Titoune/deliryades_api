<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\ChatCommentsTable|\Cake\ORM\Association\HasMany $ChatComments
 * @property \App\Model\Table\EventCommentsTable|\Cake\ORM\Association\HasMany $EventComments
 * @property \App\Model\Table\EventParticipationsTable|\Cake\ORM\Association\HasMany $EventParticipations
 * @property \App\Model\Table\EventsTable|\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\PollAnswersTable|\Cake\ORM\Association\HasMany $PollAnswers
 * @property \App\Model\Table\PollsTable|\Cake\ORM\Association\HasMany $Polls
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ChatComments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('EventComments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('EventParticipations', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('PollAnswers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Polls', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Devices', [
            'foreignKey' => 'user_id'
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
            ->scalar('firstname')
            ->lengthBetween('firstname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->notEmpty('firstname');

        $validator
            ->scalar('lastname')
            ->lengthBetween('lastname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->notEmpty('lastname');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('password1')
            ->allowEmpty('password1', 'update')
            ->lengthBetween('password1', [2, 40], 'Le champ doit contenir entre 2 et 40 caractères');


        $validator
            ->scalar('password2')
            ->allowEmpty('password2', 'update')
            ->add('password2', [
                'password2Match' => [
                    'rule' => ['compareWith', 'password1'],
                    'message' => 'Les mots de passe ne correspondent pas',
                ]
            ]);

        $validator
            ->dateTime('logged')
            ->allowEmpty('logged');

        $validator
            ->scalar('picture')
            ->maxLength('picture', 255)
            ->allowEmpty('picture');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 1)
            ->allowEmpty('sex')
            ->inList('sex', ['m', 'f', 'i'], 'veuillez choisir une option dans la liste');


        $validator
            ->scalar('street_number')
            ->maxLength('street_number', 255)
            ->allowEmpty('street_number');

        $validator
            ->scalar('route')
            ->maxLength('route', 255)
            ->allowEmpty('route');

        $validator
            ->scalar('postal_code')
            ->maxLength('postal_code', 255)
            ->allowEmpty('postal_code');

        $validator
            ->scalar('locality')
            ->maxLength('locality', 255)
            ->allowEmpty('locality');

        $validator
            ->scalar('country')
            ->maxLength('country', 255)
            ->allowEmpty('country');

        $validator
            ->scalar('country_short')
            ->maxLength('country_short', 255)
            ->allowEmpty('country_short');

        $validator
            ->decimal('lat')
            ->allowEmpty('lat');

        $validator
            ->decimal('lng')
            ->allowEmpty('lng');

        $validator
            ->scalar('cellphone')
            ->allowEmpty('cellphone')
            ->add('cellphone', 'cellphoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[6-7](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de mobile français sont acceptés"
            ]);

        $validator
            ->scalar('phone')
            ->allowEmpty('phone')
            ->add('phone', 'phoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de téléphone français sont acceptés"
            ]);

        $validator
            ->date('birth', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('birth');

        $validator
            ->date('death', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('death');

        $validator
            ->scalar('presentation')
            ->allowEmpty('presentation')
            ->maxLength('presentation', 10000);


        $validator
            ->scalar('branch')
            ->maxLength('branch', 255)
            ->allowEmpty('branch');

        $validator
            ->scalar('profession')
            ->maxLength('profession', 255)
            ->allowEmpty('profession');

        $validator
            ->allowEmpty('admin');

        $validator
            ->allowEmpty('notification_cellphone_anniversary');

        $validator
            ->allowEmpty('notification_cellphone_event');

        $validator
            ->allowEmpty('notification_cellphone_poll');

        $validator
            ->allowEmpty('notification_email_anniversary');

        $validator
            ->allowEmpty('notification_email_poll');

        $validator
            ->allowEmpty('notification_email_event');


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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
