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
            ->maxLength('firstname', 255)
            ->allowEmpty('firstname');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->allowEmpty('lastname');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->allowEmpty('password');

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
            ->allowEmpty('sex');

        $validator
            ->scalar('token')
            ->maxLength('token', 255)
            ->allowEmpty('token');

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
            ->maxLength('cellphone', 255)
            ->allowEmpty('cellphone');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 255)
            ->allowEmpty('phone');

        $validator
            ->date('birth')
            ->allowEmpty('birth');

        $validator
            ->date('death')
            ->allowEmpty('death');

        $validator
            ->scalar('presentation')
            ->allowEmpty('presentation');

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

        $validator
            ->scalar('device_push_token')
            ->maxLength('device_push_token', 255)
            ->allowEmpty('device_push_token');

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
