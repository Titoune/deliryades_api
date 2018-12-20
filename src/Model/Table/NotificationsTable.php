<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Notifications Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\NotificationTypesTable|\Cake\ORM\Association\BelongsTo $NotificationTypes
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\Notification get($primaryKey, $options = [])
 * @method \App\Model\Entity\Notification newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Notification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Notification|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Notification[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Notification findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotificationsTable extends Table
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

        $this->setTable('notifications');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', [
            'UserCities' => [
                'notification_count' => ['conditions' => ['Notifications.user_city_id IS NOT ' => null]],
                'unread_notification_count' => ['conditions' => ['Notifications.user_city_id IS NOT ' => null, 'Notifications.readed' => 0]]
            ],
            'Mayors' => [
                'notification_count' => ['conditions' => ['Notifications.mayor_id IS NOT ' => null]],
                'unread_notification_count' => ['conditions' => ['Notifications.mayor_id IS NOT ' => null, 'Notifications.readed' => 0]]
            ],
            'Mandataries' => [
                'notification_count' => ['conditions' => ['Notifications.mandatary_id IS NOT ' => null]],
                'unread_notification_count' => ['conditions' => ['Notifications.mandatary_id IS NOT ' => null, 'Notifications.readed' => 0]]
            ],
            'Administrators' => [
                'notification_count' => ['conditions' => ['Notifications.administrator_id IS NOT ' => null]],
                'unread_notification_count' => ['conditions' => ['Notifications.administrator_id IS NOT ' => null, 'Notifications.readed' => 0]]
            ]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('NotificationTypes', [
            'foreignKey' => 'notification_type_id',

        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);

        $this->belongsTo('UserCities', [
            'foreignKey' => 'user_city_id',

        ]);

        $this->belongsTo('Mayors', [
            'foreignKey' => 'mayor_id',

        ]);

        $this->belongsTo('Mandataries', [
            'foreignKey' => 'mandatary_id',

        ]);

        $this->belongsTo('Administrators', [
            'foreignKey' => 'administrator_id',

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
            ->scalar('sender')
            ->maxLength('sender', 45)
            ->allowEmpty('sender');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmpty('title');

        $validator
            ->boolean('sent')
            ->allowEmpty('sent');

        $validator
            ->boolean('readed')
            ->allowEmpty('readed');

        $validator
            ->dateTime('published')
            ->allowEmpty('published');

        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('sent_firebase')
            ->allowEmpty('sent_firebase');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['notification_type_id'], 'NotificationTypes'));
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

        $notification = $this->find()->contain(['NotificationTypes'])->where(['Notifications.id' => $entity->id])->first();
        if ($notification) {
            if ($entity->isNew()) {
                (new Socket())->emit('/perso-' . $entity->user_id, 'notification-create', ['notification' => $notification]);
            } else {
                (new Socket())->emit('/perso-' . $entity->user_id, 'notification-update', ['notification' => $notification]);
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

        (new Socket())->emit('/perso-' . $entity->user_id, 'notification-delete', ['notification' => $entity]);
        $notification_count = $this->find()->where(['Notifications.readed' => 0, 'Notifications.mayor_id' => $entity->mayor_id, 'Notifications.user_city_id' => $entity->user_city_id, 'Notifications.mandatary_id' => $entity->mandatary_id, 'Notifications.administrator_id' => $entity->administrator_id])->count();
        $foreign_id = null;
        $user_type = null;
        if ($entity->user_city_id) {
            $user_type = 'citizen';
        } elseif ($entity->mayor_id) {
            $user_type = 'mayor';
        } elseif ($entity->mandatary_id) {
            $user_type = 'mandatary';
        } elseif ($entity->administrator_id) {
            $user_type = 'administrator';
        }
        (new Socket())->emit('/perso-' . $entity->user_id, 'notification-count', ['user_type' => $user_type, 'notification_count' => $notification_count]);
    }


}
