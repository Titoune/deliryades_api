<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CityContacts Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\CityContact get($primaryKey, $options = [])
 * @method \App\Model\Entity\CityContact newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CityContact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CityContact|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CityContact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CityContact[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CityContact findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CityContactsTable extends Table
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

        $this->setTable('city_contacts');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

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
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmpty('name');

        $validator
            ->scalar('role')
            ->maxLength('role', 255)
            ->allowEmpty('role');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 45)
            ->allowEmpty('phone');

        $validator
            ->scalar('cellphone')
            ->maxLength('cellphone', 45)
            ->allowEmpty('cellphone');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('description')
            ->allowEmpty('description');

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

        return $rules;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->uniqid) {

        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $city_contact = $this->find()->where(['CityContacts.id' => $entity->id])->first();
        if ($city_contact) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'city-contact-create', ['city_contact' => $city_contact]);
            } else {
                (new Socket())->emit('/administrator', 'city-contact-update', ['city_contact' => $city_contact]);
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

        (new Socket())->emit('/administrator', 'city-contact-delete', ['city_contact' => $entity]);
    }


}
