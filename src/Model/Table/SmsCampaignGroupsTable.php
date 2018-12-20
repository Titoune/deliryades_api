<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SmsCampaignGroups Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\SmsCampaignGroupUsersTable|\Cake\ORM\Association\HasMany $SmsCampaignGroupUsers
 * @property \App\Model\Table\SmsCampaignsTable|\Cake\ORM\Association\BelongsToMany $SmsCampaigns
 *
 * @method \App\Model\Entity\SmsCampaignGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroup findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SmsCampaignGroupsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('sms_campaign_groups');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsToMany('SmsCampaigns');

        $this->hasMany('SmsCampaignGroupUsers', [
            'foreignKey' => 'sms_campaign_group_id'
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
            ->notEmpty('name', 'Ce champ est requis')
            ->lengthBetween('name', [2, 255], 'Ce champ doit contenir entre 2 et 255 caractères');

        $validator
            ->scalar('description')
            ->lengthBetween('description', [2, 1000], 'Ce champ doit contenir entre 2 et 1000 caractères')
            ->allowEmpty('description');

        $validator
            ->boolean('open')
            ->notEmpty('open');


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

        $sms_campaign_group = $this->find()->where(['SmsCampaignGroups.id' => $entity->id])->first();
        if ($sms_campaign_group) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-group-create', ['sms_campaign_group' => $sms_campaign_group]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-group-update', ['sms_campaign_group' => $sms_campaign_group]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'sms-campaign-group-delete', ['sms_campaign_group' => $entity]);
    }


}
