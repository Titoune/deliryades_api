<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SmsCampaignGroupUsers Model
 *
 * @property \App\Model\Table\SmsCampaignGroupsTable|\Cake\ORM\Association\BelongsTo $SmsCampaignGroups
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\SmsCampaignGroupUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SmsCampaignGroupUser findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class SmsCampaignGroupUsersTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('sms_campaign_group_users');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'SmsCampaignGroups' => ['sms_campaign_group_user_count']
        ]);
        $this->belongsTo('SmsCampaignGroups', [
            'foreignKey' => 'sms_campaign_group_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

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
            ->scalar('firstname')
            ->notEmpty('firstname', 'Ce champ est requis')
            ->lengthBetween('firstname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->add('firstname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->scalar('lastname')
            ->notEmpty('lastname', 'Ce champ est requis')
            ->lengthBetween('lastname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->add('lastname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->scalar('cellphone')
            ->add('cellphone', 'cellphoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[6-7](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de mobile français sont acceptés"
            ])
            ->allowEmpty('cellphone');

        $validator
            ->scalar('information')
            ->maxLength('information', 255)
            ->allowEmpty('information');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['sms_campaign_group_id'], 'SmsCampaignGroups'));
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

        $sms_campaign_group_user = $this->find()->contain(['SmsCampaignGroups'])->where(['SmsCampaignGroupUsers.id' => $entity->id])->first();
        if ($sms_campaign_group_user) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $sms_campaign_group_user->sms_campaign_group->city_id, 'sms-campaign-group-user-create', ['sms_campaign_group_user' => $sms_campaign_group_user]);
            } else {
                (new Socket())->emit('/dynamic-' . $sms_campaign_group_user->sms_campaign_group->city_id, 'sms-campaign-group-user-update', ['sms_campaign_group_user' => $sms_campaign_group_user]);
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

        $sms_campaign_group = $this->SmsCampaignGroups->find()->where(['SmsCampaignGroups.id' => $entity->sms_campaign_group_id])->first();
        if ($sms_campaign_group) {
            (new Socket())->emit('/dynamic-' . $sms_campaign_group->city_id, 'sms-campaign-group-user-delete', ['sms_campaign_group_user' => $entity]);
        }
    }

}
