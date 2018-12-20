<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdsCities Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsTo $Ads
 * @property \App\Model\Table\AdTransfersTable|\Cake\ORM\Association\BelongsTo $AdTransfers
 * @property \App\Model\Table\AdClicksTable|\Cake\ORM\Association\HasMany $AdClicks
 * @property \App\Model\Table\AdDisplaysTable|\Cake\ORM\Association\HasMany $AdDisplays
 *
 * @method \App\Model\Entity\AdsCity get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdsCity newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdsCity[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdsCity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdsCity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdsCity[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdsCity findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdsCitiesTable extends Table
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

        $this->setTable('ads_cities');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Ads' => ['city_count'],
            'Cities' => ['ad_count']
        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->belongsTo('Ads', [
            'foreignKey' => 'ad_id',

        ]);
        $this->hasMany('AdClicks', [
            'foreignKey' => 'ads_city_id'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->decimal('transfer_amount')
            ->allowEmpty('transfer_amount');

        $validator
            ->allowEmpty('coef');

        $validator
            ->decimal('price_ht')
            ->notEmpty('price_ht');

        $validator
            ->decimal('rate_payment')
            ->notEmpty('rate_payment');


        $validator
            ->dateTime('reclamation_date')
            ->allowEmpty('reclamation_date');

        $validator
            ->scalar('reclamation_content')
            ->allowEmpty('reclamation_content');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['city_id'], 'Cities'));
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

        $ads_city = $this->find()->contain(['Ads.AdClients'])->where(['AdsCities.id' => $entity->id])->first();
        if ($ads_city) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'ads-city-create', ['ads_city' => $ads_city]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'ads-city-update', ['ads_city' => $ads_city]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'ads-city-delete', ['ads_city' => $entity]);
    }


}
