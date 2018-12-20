<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SmsCampaignSms Model
 *
 * @property \App\Model\Table\SmsCampaignsTable|\Cake\ORM\Association\BelongsTo $SmsCampaigns
 *
 * @method \App\Model\Entity\SmsCampaignSm get($primaryKey, $options = [])
 * @method \App\Model\Entity\SmsCampaignSm newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SmsCampaignSm[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignSm|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SmsCampaignSm patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignSm[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignSm findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SmsCampaignSmsTable extends Table
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

        $this->setTable('sms_campaign_sms');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'SmsCampaigns' => [
                'sms_count',
                'sms_sent_count' => ['conditions' => ['SmsCampaignSms.status' => 'OK']]
            ]
        ]);

        $this->belongsTo('SmsCampaigns', [
            'foreignKey' => 'sms_campaign_id',

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
            ->scalar('cellphone')
            ->maxLength('cellphone', 45)
            ->allowEmpty('cellphone');

        $validator
            ->scalar('status')
            ->maxLength('status', 2)
            ->allowEmpty('status');

        $validator
            ->scalar('details')
            ->allowEmpty('details');

        $validator
            ->scalar('reference')
            ->maxLength('reference', 45)
            ->allowEmpty('reference');

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 255)
            ->allowEmpty('firstname');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->allowEmpty('lastname');

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
        $rules->add($rules->existsIn(['sms_campaign_id'], 'SmsCampaigns'));

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

        $sms_campaign_sms = $this->find()->contain(['SmsCampaigns'])->where(['SmsCampaignSms.id' => $entity->id])->first();
        if ($sms_campaign_sms) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $sms_campaign_sms->sms_campaign->city_id, 'sms-campaign-sms-create', ['sms_campaign_sms' => $sms_campaign_sms]);
            } else {
                (new Socket())->emit('/dynamic-' . $sms_campaign_sms->sms_campaign->city_id, 'sms-campaign-sms-update', ['sms_campaign_sms' => $sms_campaign_sms]);
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

        $sms_campaign = $this->SmsCampaigns->find()->where(['SmsCampaigns.id' => $entity->sms_campaign_id])->first();
        if ($sms_campaign) {
            (new Socket())->emit('/dynamic-' . $sms_campaign->city_id, 'sms-campaign-sms-delete', ['sms_campaign_sms' => $entity]);
        }
    }


}
