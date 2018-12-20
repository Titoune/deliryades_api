<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NotificationTypes Model
 *
 * @property \App\Model\Table\NotificationsTable|\Cake\ORM\Association\HasMany $Notifications
 *
 * @method \App\Model\Entity\NotificationType get($primaryKey, $options = [])
 * @method \App\Model\Entity\NotificationType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NotificationType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NotificationType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotificationType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NotificationType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NotificationType findOrCreate($search, callable $callback = null, $options = [])
 */
class NotificationTypesTable extends Table
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

        $this->setTable('notification_types');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->hasMany('Notifications', [
            'foreignKey' => 'notification_type_id'
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
            ->maxLength('name', 255)
            ->allowEmpty('name');

        $validator
            ->scalar('model')
            ->maxLength('model', 45)
            ->allowEmpty('model');

        $validator
            ->scalar('prefix')
            ->maxLength('prefix', 45)
            ->allowEmpty('prefix');

        $validator
            ->scalar('controller')
            ->maxLength('controller', 45)
            ->allowEmpty('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 45)
            ->allowEmpty('action');

        $validator
            ->scalar('email_subject')
            ->allowEmpty('email_subject');

        $validator
            ->scalar('email_message')
            ->allowEmpty('email_message');

        $validator
            ->scalar('notification_title')
            ->maxLength('notification_title', 255)
            ->allowEmpty('notification_title');

        $validator
            ->scalar('notification_message')
            ->allowEmpty('notification_message');

        $validator
            ->scalar('type')
            ->maxLength('type', 45)
            ->allowEmpty('type');

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

        $notification_type = $this->find()->where(['NotificationTypes.id' => $entity->id])->first();
        if ($notification_type) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'notification-type-create', ['notification_type' => $notification_type]);
            } else {
                (new Socket())->emit('/administrator', 'notification-type-update', ['notification_type' => $notification_type]);
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

        (new Socket())->emit('/administrator', 'notification-type-delete', ['notification-type' => $entity]);
    }
}
