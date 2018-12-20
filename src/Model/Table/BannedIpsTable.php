<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BannedIps Model
 *
 * @method \App\Model\Entity\BannedIp get($primaryKey, $options = [])
 * @method \App\Model\Entity\BannedIp newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BannedIp[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BannedIp|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BannedIp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BannedIp[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BannedIp findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BannedIpsTable extends Table
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

        $this->setTable('banned_ips');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
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
            ->scalar('ip_address')
            ->maxLength('ip_address', 15)
            ->allowEmpty('ip_address');

        return $validator;
    }

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

        $banned_ip = $this->find()->where(['BannedIps.id' => $entity->id])->first();
        if ($banned_ip) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'banned-ip-create', ['banned_ip' => $banned_ip]);
            } else {
                (new Socket())->emit('/administrator', 'banned-ip-update', ['banned_ip' => $banned_ip]);
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

        (new Socket())->emit('/administrator', 'banned-ip-delete', ['banned_ip' => $entity]);
    }
}
