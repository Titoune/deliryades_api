<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DiscussionUsers Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DiscussionsTable|\Cake\ORM\Association\BelongsTo $Discussions
 *
 * @method \App\Model\Entity\DiscussionUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\DiscussionUser newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DiscussionUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionUser|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscussionUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionUser[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionUser findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DiscussionUsersTable extends Table
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

        $this->setTable('discussion_users');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

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
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $discussion_user = $this->find()->where(['DiscussionUsers.id' => $entity->id])->first();
        if ($discussion_user) {
            if ($entity->isNew()) {
                (new Socket())->emit('/perso-' . $entity->user_id, 'discussion-user-create', ['discussion_user' => $discussion_user]);
            } else {
                (new Socket())->emit('/perso-' . $entity->user_id, 'discussion-user-update', ['discussion_user' => $discussion_user]);
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

        (new Socket())->emit('/perso-' . $entity->user_id, 'discussion-user-delete', ['discussion_user' => $entity]);
    }


}
