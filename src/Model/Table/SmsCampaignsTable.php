<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SmsCampaigns Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\SmsCampaignSmsTable|\Cake\ORM\Association\HasMany $SmsCampaignSms
 * @property \App\Model\Table\SmsCampaignGroupsTable|\Cake\ORM\Association\BelongsToMany $SmsCampaignGroups
 *
 * @method \App\Model\Entity\SmsCampaign get($primaryKey, $options = [])
 * @method \App\Model\Entity\SmsCampaign newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SmsCampaign[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaign|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SmsCampaign patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaign[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaign findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SmsCampaignsTable extends Table
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

        $this->setTable('sms_campaigns');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['sms_campaign_count']]);


        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);

        $this->belongsToMany('SmsCampaignGroups');


        $this->hasMany('SmsCampaignSms', [
            'foreignKey' => 'sms_campaign_id'
        ]);

        $this->hasMany('SmsCampaignGroupsSmsCampaigns', [
            'foreignKey' => 'sms_campaign_id'
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
            ->scalar('uniqid')
            ->maxLength('uniqid', 255)
            ->allowEmpty('uniqid');

        $validator
            ->scalar('name')
            ->notEmpty('name', 'Ce champ est requis')
            ->lengthBetween('name', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères');

        $validator
            ->scalar('content')
            ->notEmpty('content', 'Ce champ est requis')
            ->lengthBetween('content', [1, 140], 'Le champ doit contenir entre 2 et 140 caractères');

        $validator
            ->decimal('price_estimation_ht')
            ->allowEmpty('price_estimation_ht');

        $validator
            ->decimal('price_ht')
            ->allowEmpty('price_ht');

        $validator
            ->allowEmpty('status');

        $validator
            ->boolean('invoiced')
            ->allowEmpty('invoiced');

        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('notified')
            ->allowEmpty('notified');

        $validator
            ->integer('sms_campaign_type')
            ->notEmpty('sms_campaign_type');

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
            $entity->uniqid = Tools::_getRandomHash();
        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $sms_campaign = $this->find()->where(['SmsCampaigns.id' => $entity->id])->first();
        if ($sms_campaign) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-create', ['sms_campaign' => $sms_campaign]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-update', ['sms_campaign' => $sms_campaign]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-delete', ['sms_campaign' => $entity]);
    }


}
