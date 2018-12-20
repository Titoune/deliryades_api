<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Administrators Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Administrator get($primaryKey, $options = [])
 * @method \App\Model\Entity\Administrator newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Administrator[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Administrator|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Administrator patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class AdministratorsTable extends Table
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

        $this->setTable('administrators');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Users' => ['administrator_count' => ['conditions' => ['Administrators.deleted IS ' => null]]]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);

        $this->hasMany('Notifications');
        $this->hasMany('AdministratorDevices');

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
            ->boolean('activated')
            ->notEmpty('activated');

        $validator
            ->dateTime('logged')
            ->allowEmpty('logged');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->boolean('salesman')
            ->notEmpty('salesman');

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

        $administrator = $this->find()->where(['Administrators.id' => $entity->id])->first();
        if ($administrator) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'administrator-create', ['administrator' => $administrator]);
            } else {
                (new Socket())->emit('/administrator', 'administrator-update', ['administrator' => $administrator]);
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

        (new Socket())->emit('/administrator', 'administrator-delete', ['administrator' => $entity]);
    }


}
