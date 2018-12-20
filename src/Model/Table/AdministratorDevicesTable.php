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
class AdministratorDevicesTable extends Table
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

        $this->setTable('administrator_devices');
        $this->setPrimaryKey('id');
        $this->addBehavior('Log');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Administrators');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
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

        $administrator_device = $this->find()->where(['AdministratorDevices.id' => $entity->id])->first();
        if ($administrator_device) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'administrator-device-create', ['administrator_device' => $administrator_device]);
            } else {
                (new Socket())->emit('/administrator', 'administrator-device-update', ['administrator_device' => $administrator_device]);
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

        (new Socket())->emit('/administrator', 'administrator-device-create', ['administrator_device' => $entity]);
    }
}
