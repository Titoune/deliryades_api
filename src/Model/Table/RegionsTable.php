<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Regions Model
 *
 * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\HasMany $Departments
 *
 * @method \App\Model\Entity\Region get($primaryKey, $options = [])
 * @method \App\Model\Entity\Region newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Region[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Region|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Region patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Region[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Region findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RegionsTable extends Table
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

        $this->setTable('regions');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->hasMany('Departments', [
            'foreignKey' => 'region_id'
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
            ->scalar('name')
            ->maxLength('name', 155)
            ->allowEmpty('name');

        return $validator;
    }


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

        $region = $this->find()->where(['Regions.id' => $entity->id])->first();
        if ($region) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'region-create', ['region' => $region]);
            } else {
                (new Socket())->emit('/administrator', 'region-update', ['region' => $region]);
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

        (new Socket())->emit('/administrator', 'region-delete', ['region' => $entity]);
    }
}
