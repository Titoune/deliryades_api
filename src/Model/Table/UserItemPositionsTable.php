<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserItemPositions Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\UserItemPositionsTable|\Cake\ORM\Association\BelongsTo $ParentUserItemPositions
 * @property \App\Model\Table\UserItemPositionsTable|\Cake\ORM\Association\HasMany $ChildUserItemPositions
 *
 * @method \App\Model\Entity\UserItemPosition get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserItemPosition newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserItemPosition[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserItemPosition|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserItemPosition patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserItemPosition[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserItemPosition findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserItemPositionsTable extends Table
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

        $this->setTable('user_item_positions');
        $this->setPrimaryKey('id');
        $this->addBehavior('Log');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users');
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
            ->scalar('model')
            ->maxLength('model', 255)
            ->notEmpty('model');

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
            $entity->uniqid = Tools::_getRandomHash();
        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $user_item_position = $this->find()->where(['UserItemPositions.id' => $entity->id])->first();
        if ($user_item_position) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'user-item-position-create', ['user_item_position' => $user_item_position]);
            } else {
                (new Socket())->emit('/administrator', 'user-item-position-update', ['user_item_position' => $user_item_position]);
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

        (new Socket())->emit('/administrator', 'user-item-position-delete', ['user_item_position' => $entity]);
    }

}
