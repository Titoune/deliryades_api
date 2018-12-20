<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoginAttempts Model
 *
 * @method \App\Model\Entity\LoginAttempt get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoginAttempt newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoginAttempt[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoginAttempt|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoginAttempt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoginAttempt[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoginAttempt findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoginAttemptsTable extends Table
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

        $this->setTable('login_attempts');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'Users' => ['login_attempt_count']
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'login',
            'bindingKey' => 'email',
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
            ->scalar('login')
            ->maxLength('login', 155)
            ->allowEmpty('login');

        $validator
            ->scalar('ip')
            ->maxLength('ip', 45)
            ->allowEmpty('ip');

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

        $login_attempt = $this->find()->where(['LoginAttempts.id' => $entity->id])->first();
        if ($login_attempt) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'login-attempt-create', ['login_attempt' => $login_attempt]);
            } else {
                (new Socket())->emit('/administrator', 'login-attempt-update', ['login_attempt' => $login_attempt]);
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

        (new Socket())->emit('/administrator', 'login-attempt-delete', ['login_attempt' => $entity]);
    }


}
