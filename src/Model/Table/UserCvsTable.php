<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * UserCvs Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserCv get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserCv newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserCv[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserCv|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserCv patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserCv[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserCv findOrCreate($search, callable $callback = null, $options = [])
 */
class UserCvsTable extends Table
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

        $this->setTable('user_cvs');

        $this->setPrimaryKey('id');
        $this->addBehavior('Log');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

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
            ->scalar('title')
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [10, 255], "Le champs doit contenir entre 10 et 255 caractères");

        $validator
            ->numeric('year')
            ->range('year', [1900, date('Y')], "L'année  doit être comprise en entre 1900 et " . date('Y'))
            ->notEmpty('year', 'Ce champ est requis');

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

        $table = TableRegistry::get('Mayors');
        $mayor = $table->find()->where(['Mayors.user_id' => $entity->user_id])->first();
        if ($mayor) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $mayor->city_id, 'user-cv-create', ['user_cv' => $entity]);
            } else {
                (new Socket())->emit('/dynamic-' . $mayor->city_id, 'user-cv-update', ['user_cv' => $entity]);
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

        $table = TableRegistry::get('Mayors');
        $mayor = $table->find()->where(['Mayors.user_id' => $entity->user_id])->first();
        if ($mayor) {
            (new Socket())->emit('/dynamic-' . $mayor->city_id, 'user-cv-delete', ['user_cv' => $entity]);
        }
    }


}
