<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ContractRenewalsTable extends Table
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

        $this->setTable('contract_renewals');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);

        $this->addBehavior('CounterCache', [
            'Cities' => ['contract_renewal_count']
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
            ->date('start', 'ymd', 'Le champ doit être une date valide')
            ->notEmpty('start', 'Ce champ est requis');

        $validator
            ->date('end', 'ymd', 'Le champ doit être une date valide')
            ->notEmpty('end', 'Ce champ est requis');

        $validator
            ->decimal('monthly_price', null, 'Ce champ doit être au format décimal')
            ->allowEmpty('monthly_price');

        $validator
            ->decimal('fee', null, 'Ce champ doit être au format décimal')
            ->allowEmpty('fee');

        $validator
            ->date('paid', 'ymd', 'Le champ doit être une date valide')
            ->notEmpty('paid', 'Ce champ est requis');

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

        $contract_renewal = $this->find()->where(['ContractRenewals.id' => $entity->id])->first();
        if ($contract_renewal) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'contract-renewal-create', ['contract_renewal' => $contract_renewal]);
            } else {
                (new Socket())->emit('/administrator', 'contract-renewal-update', ['contract_renewal' => $contract_renewal]);
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

        (new Socket())->emit('/administrator', 'contract-renewal-delete', ['contract_renewal' => $entity]);
    }
}
