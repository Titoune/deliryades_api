<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdExchanges Model
 *
 * @property \App\Model\Table\AdClientsTable|\Cake\ORM\Association\BelongsTo $AdClients
 *
 * @method \App\Model\Entity\AdExchange get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdExchange newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdExchange[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdExchange|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdExchange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdExchange[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdExchange findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdExchangesTable extends Table
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
        $this->setTable('ad_exchanges');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'AdClients' => ['exchange_count']
        ]);

        $this->belongsTo('AdClients', [
            'foreignKey' => 'ad_client_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('content')
            ->notEmpty('content');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['ad_client_id'], 'AdClients'));
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

        $ad_exchange = $this->find()->where(['AdExchanges.id' => $entity->id])->first();
        if ($ad_exchange) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-exchange-create', ['ad_exchange' => $ad_exchange]);
            } else {
                (new Socket())->emit('/administrator', 'ad-exchange-update', ['ad_exchange' => $ad_exchange]);
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

        (new Socket())->emit('/administrator', 'ad-exchange-delete', ['ad_exchange' => $entity]);
    }
}
