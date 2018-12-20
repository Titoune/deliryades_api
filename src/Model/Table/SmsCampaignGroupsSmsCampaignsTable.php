<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SmsCampaignGroupsSmsCampaigns Model
 *
 * @property \App\Model\Table\SmsCampaignGroupsTable|\Cake\ORM\Association\BelongsTo $SmsCampaignGroups
 * @property \App\Model\Table\SmsCampaignsTable|\Cake\ORM\Association\BelongsTo $SmsCampaigns
 *
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign get($primaryKey, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupsSmsCampaign findOrCreate($search, callable $callback = null, $options = [])
 */
class SmsCampaignGroupsSmsCampaignsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('sms_campaign_groups_sms_campaigns');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('SmsCampaigns');
        $this->belongsTo('SmsCampaignGroups');
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

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
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

        $sms_campaign_groups_sms_campaign = $this->find()->where(['SmsCampaignGroupsSmsCampaigns.id' => $entity->id])->first();
        if ($sms_campaign_groups_sms_campaign) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'sms-campaign-groups-sms-campaign-create', ['sms_campaign_groups_sms_campaign' => $sms_campaign_groups_sms_campaign]);
            } else {
                (new Socket())->emit('/administrator', 'sms-campaign-groups-sms-campaign-update', ['sms_campaign_groups_sms_campaign' => $sms_campaign_groups_sms_campaign]);
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

        (new Socket())->emit('/administrator', 'sms-campaign-groups-sms-campaign-delete', ['sms_campaign_groups_sms_campaign' => $entity]);
    }


}
