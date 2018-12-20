<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdInvoices Model
 *
 * @property \App\Model\Table\AdClientsTable|\Cake\ORM\Association\BelongsTo $AdClients
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsTo $Ads
 *
 * @method \App\Model\Entity\AdInvoice get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdInvoice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdInvoice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdInvoice|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdInvoice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdInvoice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdInvoice findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdInvoicesTable extends Table
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

        $this->setTable('ad_invoices');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'AdClients' => ['invoice_count'],
            'Ads' => ['invoice_count', 'paid_invoice' => ['conditions' => ['paid' => 1]]]
        ]);

        $this->belongsTo('AdClients');
        $this->belongsTo('Ads');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('num')
            ->notEmpty('num');

        $validator
            ->scalar('company')
            ->maxLength('company', 155)
            ->allowEmpty('company');

        $validator
            ->scalar('name')
            ->maxLength('name', 155)
            ->allowEmpty('name');

        $validator
            ->decimal('total_ht')
            ->notEmpty('total_ht');

        $validator
            ->decimal('vat')
            ->notEmpty('vat');

        $validator
            ->decimal('total_vat')
            ->notEmpty('total_vat');

        $validator
            ->decimal('total_ttc')
            ->notEmpty('total_ttc');

        $validator
            ->dateTime('date_start')
            ->allowEmpty('date_start');

        $validator
            ->scalar('type')
            ->maxLength('type', 155)
            ->allowEmpty('type');

        $validator
            ->dateTime('expiration_date')
            ->allowEmpty('expiration_date');

        $validator
            ->integer('expiration_click')
            ->allowEmpty('expiration_click');

        $validator
            ->integer('expiration_display')
            ->allowEmpty('expiration_display');

        $validator
            ->scalar('street_number')
            ->maxLength('street_number', 155)
            ->allowEmpty('street_number');

        $validator
            ->scalar('route')
            ->maxLength('route', 155)
            ->allowEmpty('route');

        $validator
            ->scalar('postal_code')
            ->maxLength('postal_code', 155)
            ->allowEmpty('postal_code');

        $validator
            ->dateTime('date_paid')
            ->allowEmpty('date_paid');

        $validator
            ->scalar('comment')
            ->allowEmpty('comment');

        $validator
            ->boolean('paid')
            ->notEmpty('paid');

        $validator
            ->scalar('locality')
            ->maxLength('locality', 155)
            ->allowEmpty('locality');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['ad_client_id'], 'AdClients'));
        $rules->add($rules->existsIn(['ad_id'], 'Ads'));

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

        $ad_invoice = $this->find()->where(['AdInvoices.id' => $entity->id])->first();
        if ($ad_invoice) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-invoice-create', ['ad_invoice' => $ad_invoice]);
            } else {
                (new Socket())->emit('/administrator', 'ad-invoice-update', ['ad_invoice' => $ad_invoice]);
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

        (new Socket())->emit('/administrator', 'ad-invoice-delete', ['ad_invoice' => $entity]);
    }
}
