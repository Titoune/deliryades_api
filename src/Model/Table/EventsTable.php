<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Events Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\EventParticipationsTable|\Cake\ORM\Association\HasMany $EventParticipations
 *
 * @method \App\Model\Entity\Event get($primaryKey, $options = [])
 * @method \App\Model\Entity\Event newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Event[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Event|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Event[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Event findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EventsTable extends Table
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

        $this->setTable('events');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('EventParticipations', [
            'foreignKey' => 'event_id'
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
            ->scalar('title')
            ->lengthBetween('title', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères')
            ->notEmpty('title');

        $validator
            ->scalar('content')
            ->notEmpty('content')
            ->lengthBetween('content', [2, 2000], 'Le champ doit contenir entre 2 et 2000 caractères');


        $validator
            ->dateTime('start', 'ymd', null, 'Le champ doit être une date valide')
            ->notEmpty('start');

        $validator
            ->dateTime('end', 'ymd', null, 'Le champ doit être une date valide')
            ->notEmpty('end');

        $validator
            ->decimal('price')
            ->allowEmpty('price');

        $validator
            ->email('email')
            ->allowEmpty('email');

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
}
