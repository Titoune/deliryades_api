<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * AdminExchanges Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\AdminExchange get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdminExchange newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdminExchange[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdminExchange|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdminExchange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdminExchange[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdminExchange findOrCreate($search, callable $callback = null, $options = [])
 */
class AdminExchangesTable extends Table
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

        $this->setTable('admin_exchanges');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['exchange_count']]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

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
            ->scalar('receiver')
            ->notEmpty('receiver', 'Ce champ est requis')
            ->lengthBetween('receiver', [2, 155], 'Le champ doit contenir entre 2 et 150 caractères');

        $validator
            ->scalar('content')
            ->allowEmpty('content');

        $validator
            ->dateTime('date', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('date');

        $validator
            ->integer('type')
            ->notEmpty('type', 'Ce champ est requis');

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

        $admin_echange = $this->find()->where(['AdminExchanges.id' => $entity->id])->first();
        if ($admin_echange) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'admin-exchange-create', ['admin_echange' => $admin_echange]);
            } else {
                (new Socket())->emit('/administrator', 'admin-exchange-update', ['admin_echange' => $admin_echange]);
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

        (new Socket())->emit('/administrator', 'admin-exchange-delete', ['admin_echange' => $entity]);
    }
}
