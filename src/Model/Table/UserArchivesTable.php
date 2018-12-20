<?php

namespace App\Model\Table;

use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserArchives Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserArchive get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserArchive newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserArchive[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserArchive|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserArchive patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserArchive[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserArchive findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserArchivesTable extends Table
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

        $this->setTable('user_archives');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Users');

    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('uniqid')
            ->maxLength('uniqid', 255)
            ->notEmpty('uniqid');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('email_notification')
            ->maxLength('email_notification', 155)
            ->allowEmpty('email_notification');

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 255)
            ->allowEmpty('firstname');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->allowEmpty('lastname');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 255)
            ->allowEmpty('phone');

        $validator
            ->date('birth')
            ->allowEmpty('birth');

        $validator
            ->scalar('cellphone')
            ->maxLength('cellphone', 255)
            ->allowEmpty('cellphone');

        $validator
            ->scalar('presentation')
            ->allowEmpty('presentation');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 1)
            ->allowEmpty('sex');

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

    }

}
