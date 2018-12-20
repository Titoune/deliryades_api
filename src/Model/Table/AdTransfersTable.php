<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdTransfers Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\AdsCitiesTable|\Cake\ORM\Association\HasMany $AdsCities
 *
 * @method \App\Model\Entity\AdTransfer get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdTransfer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdTransfer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdTransfer|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdTransfer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdTransfer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdTransfer findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdTransfersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('transfers');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Cities' => ['transfer_count']
        ]);

        $this->belongsTo('Cities');

        $this->hasMany('AdsCities');

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
            ->allowEmpty('transfer_type');

        $validator
            ->decimal('total')
            ->allowEmpty('total');

        return $validator;
    }


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

        $ad_transfer = $this->find()->where(['AdTransfers.id' => $entity->id])->first();
        if ($ad_transfer) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-transfer-create', ['ad_transfer' => $ad_transfer]);
            } else {
                (new Socket())->emit('/administrator', 'ad-transfer-update', ['ad_transfer' => $ad_transfer]);
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

        (new Socket())->emit('/administrator', 'ad-transfer-delete', ['ad_transfer' => $entity]);
    }
}
