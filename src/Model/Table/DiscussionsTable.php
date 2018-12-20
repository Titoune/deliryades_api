<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Discussions Model
 *
 * @property \App\Model\Table\DiscussionMessagesTable|\Cake\ORM\Association\HasMany $DiscussionMessages
 * @property \App\Model\Table\DiscussionUsersTable|\Cake\ORM\Association\HasMany $DiscussionUsers
 *
 * @method \App\Model\Entity\Discussion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Discussion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Discussion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Discussion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Discussion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Discussion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Discussion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DiscussionsTable extends Table
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

        $this->setTable('discussions');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->hasMany('DiscussionMessages', [
            'foreignKey' => 'discussion_id'
        ]);
        $this->hasMany('DiscussionUsers', [
            'foreignKey' => 'discussion_id'
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

        return $validator;
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

        $discussion = $this->find()->contain(['DiscussionUsers'])->where(['Discussions.id' => $entity->id])->first();
        if ($discussion) {
            foreach ($discussion->discussion_users AS $discussion_user) {

                if ($entity->isNew()) {
                    (new Socket())->emit('/perso-' . $discussion_user->user_id, 'discussion-create', ['discussion' => $discussion]);
                } else {
                    (new Socket())->emit('/perso-' . $discussion_user->user_id, 'discussion-update', ['discussion' => $discussion]);
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
        //(new Firestore())->delete($this->getTable(), $entity->uniqid);

        $discussion = $this->find()->contain(['DiscussionUsers'])->where(['Discussions.id' => $entity->id])->first();
        foreach ($discussion->discussion_users AS $discussion_user) {
            (new Socket())->emit('/perso-' . $discussion_user->user_id, 'discussion-delete', ['discussion' => $entity]);
        }
    }
}
