<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserCityDevices Model
 *
 * @property \App\Model\Table\UserCitiesTable|\Cake\ORM\Association\BelongsTo $UserCities
 *
 * @method \App\Model\Entity\UserCityDevice get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserCityDevice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserCityDevice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserCityDevice|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserCityDevice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserCityDevice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserCityDevice findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MayorDevicesTable extends Table
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

        $this->setTable('mayor_devices');
        $this->setPrimaryKey('id');
        $this->addBehavior('Log');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Mayors');
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
            ->scalar('device_manufacturer')
            ->maxLength('device_manufacturer', 255)
            ->allowEmpty('device_manufacturer');

        $validator
            ->scalar('device_platform')
            ->maxLength('device_platform', 255)
            ->allowEmpty('device_platform');

        $validator
            ->scalar('device_model')
            ->maxLength('device_model', 255)
            ->allowEmpty('device_model');

        $validator
            ->scalar('device_version')
            ->maxLength('device_version', 255)
            ->allowEmpty('device_version');

        $validator
            ->scalar('device_width')
            ->maxLength('device_width', 255)
            ->allowEmpty('device_width');

        $validator
            ->scalar('device_height')
            ->maxLength('device_height', 255)
            ->allowEmpty('device_height');

        $validator
            ->scalar('app_version_number')
            ->maxLength('app_version_number', 255)
            ->allowEmpty('app_version_number');

        $validator
            ->scalar('device_push_token')
            ->allowEmpty('device_push_token');

        $validator
            ->scalar('device_uuid')
            ->allowEmpty('device_uuid');

        $validator
            ->scalar('device_serial')
            ->allowEmpty('device_serial');

        $validator
            ->boolean('notification_mobile')
            ->allowEmpty('notification_mobile');

        $validator
            ->boolean('notification_mobile_user_city')
            ->allowEmpty('notification_mobile_user_city');

        $validator
            ->boolean('notification_mobile_publication_comment')
            ->allowEmpty('notification_mobile_publication_comment');

        $validator
            ->boolean('notification_mobile_suggestion')
            ->allowEmpty('notification_mobile_suggestion');

        $validator
            ->boolean('notification_mobile_suggestion_comment')
            ->allowEmpty('notification_mobile_suggestion_comment');

        $validator
            ->boolean('notification_mobile_newsgroup_comment')
            ->allowEmpty('notification_mobile_newsgroup_comment');


        $validator
            ->boolean('notification_mobile_discussion_message')
            ->allowEmpty('notification_mobile_discussion_message');


        $validator
            ->boolean('notification_mobile_cities_publication_diffusion')
            ->allowEmpty('notification_mobile_cities_publication_diffusion');

        $validator
            ->boolean('notification_mobile_signaling')
            ->allowEmpty('notification_mobile_signaling');

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

        $mayor_device = $this->find()->where(['MayorDevices.id' => $entity->id])->first();
        if ($mayor_device) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'mayor-device-create', ['mayor_device' => $mayor_device]);
            } else {
                (new Socket())->emit('/administrator', 'mayor-device-update', ['mayor_device' => $mayor_device]);
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

        (new Socket())->emit('/administrator', 'mayor-device-delete', ['mayor_device' => $entity]);
    }


}
