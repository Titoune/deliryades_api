<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ads Model
 *
 * @property \App\Model\Table\AdClientsTable|\Cake\ORM\Association\BelongsTo $AdClients
 * @property \App\Model\Table\AdClicksTable|\Cake\ORM\Association\HasMany $AdClicks
 * @property \App\Model\Table\AdDisplaysTable|\Cake\ORM\Association\HasMany $AdDisplays
 * @property \App\Model\Table\AdInvoicesTable|\Cake\ORM\Association\HasMany $AdInvoices
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsToMany $Cities
 * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\BelongsToMany $Departments
 *
 * @method \App\Model\Entity\Ad get($primaryKey, $options = [])
 * @method \App\Model\Entity\Ad newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Ad[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ad|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ad patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Ad[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ad findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class AdsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('ads');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'AdClients' => ['ad_count'],
        ]);

        $this->belongsTo('AdClients', [
            'foreignKey' => 'ad_client_id',

        ]);

        $this->belongsToMany('Departments');
        $this->belongsToMany('Cities', [
            'foreignKey' => 'ad_id',
            'targetForeignKey' => 'city_id',
            'joinTable' => 'ads_cities'
        ]);


        $this->hasMany('AdDisplays');
        $this->hasMany('AdClicks');
        $this->hasMany('AdsCities');
        $this->hasMany('AdsDepartments');
        $this->hasOne('AdInvoices');


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
            ->maxLength('name', 155)
            ->allowEmpty('name');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmpty('title');

        $validator
            ->scalar('content')
            ->allowEmpty('content');

        $validator
            ->scalar('picture')
            ->maxLength('picture', 155)
            ->allowEmpty('picture');

        $validator
            ->scalar('website')
            ->allowEmpty('website');

        $validator
            ->allowEmpty('expiration_type');

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
            ->dateTime('date_start')
            ->allowEmpty('date_start');

        $validator
            ->decimal('total_ht')
            ->allowEmpty('total_ht');

        $validator
            ->decimal('vat')
            ->allowEmpty('vat');

        $validator
            ->decimal('total_vat')
            ->allowEmpty('total_vat');

        $validator
            ->decimal('total_ttc')
            ->allowEmpty('total_ttc');

        $validator
            ->decimal('price_date')
            ->allowEmpty('price_date');

        $validator
            ->decimal('price_click')
            ->allowEmpty('price_click');

        $validator
            ->decimal('price_display')
            ->allowEmpty('price_display');

        $validator
            ->boolean('in_progress')
            ->notEmpty('in_progress');

        $validator
            ->boolean('activated')
            ->notEmpty('activated');

        $validator
            ->boolean('closed')
            ->notEmpty('closed');

        $validator
            ->integer('paid_invoice')
            ->notEmpty('paid_invoice');


        $validator
            ->notEmpty('type');

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

        $ad = $this->find()->where(['Ads.id' => $entity->id])->first();
        if ($ad) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-create', ['ad' => $ad]);
            } else {
                (new Socket())->emit('/administrator', 'ad-update', ['ad' => $ad]);
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

        (new Socket())->emit('/administrator', 'ad-delete', ['ad' => $entity]);
    }


}
