<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DiscussionMessages Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DiscussionsTable|\Cake\ORM\Association\BelongsTo $Discussions
 *
 * @method \App\Model\Entity\DiscussionMessage get($primaryKey, $options = [])
 * @method \App\Model\Entity\DiscussionMessage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DiscussionMessage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionMessage|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscussionMessage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionMessage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionMessage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DiscussionMessagesTable extends Table
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

        $this->setTable('discussion_messages');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Senders', [
            'className' => 'Users',
            'foreignKey' => 'sender_id',

        ]);

        $this->belongsTo('Receivers', [
            'className' => 'Users',
            'foreignKey' => 'receiver_id',

        ]);

        $this->belongsTo('Discussions', [
            'foreignKey' => 'discussion_id',

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
            ->scalar('content')
            ->lengthBetween('content', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->notEmpty('content');

        $validator
            ->dateTime('readed')
            ->allowEmpty('readed');

        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('notified')
            ->allowEmpty('notified');

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
        $rules->add($rules->existsIn(['sender_id'], 'Senders'));
        $rules->add($rules->existsIn(['receiver_id'], 'Receivers'));
        $rules->add($rules->existsIn(['discussion_id'], 'Discussions'));
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
        (new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $discussion_message = $this->find()->contain(['Senders', 'Discussions.DiscussionUsers.Users'])->where(['DiscussionMessages.id' => $entity->id])->first();


        if ($discussion_message) {
            foreach ($discussion_message->discussion->discussion_users AS $discussion_user) {
                if ($entity->isNew()) {
                    (new Socket())->emit('/perso-' . $discussion_user->user_id, 'discussion-message-create', ['discussion_message' => $discussion_message]);
                } else {
                    (new Socket())->emit('/perso-' . $discussion_user->user_id, 'discussion-message-update', ['discussion_message' => $discussion_message]);
                }
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
        (new Firestore())->delete($this->getTable(), $entity->uniqid);

        (new Socket())->emit('/perso-' . $entity->receiver_id, 'discussion-message-delete', ['discussion_message' => $entity]);
    }


}
