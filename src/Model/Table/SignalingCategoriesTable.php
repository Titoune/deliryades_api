<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SignalingCategories Model
 *
 * @property \App\Model\Table\SignalingsTable|\Cake\ORM\Association\HasMany $Signalings
 *
 * @method \App\Model\Entity\SignalingCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\SignalingCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SignalingCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SignalingCategory|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SignalingCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SignalingCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SignalingCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class SignalingCategoriesTable extends Table
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

        $this->setTable('signaling_categories');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->hasMany('Signalings', [
            'foreignKey' => 'signaling_category_id'
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
            ->scalar('name')
            ->maxLength('name', 155)
            ->allowEmpty('name');

        $validator
            ->scalar('icon')
            ->maxLength('icon', 155)
            ->allowEmpty('icon');

        $validator
            ->integer('position')
            ->notEmpty('position');

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

        $signaling_category = $this->find()->where(['SignalingCategories.id' => $entity->id])->first();
        if ($signaling_category) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'signaling-category-create', ['signaling_category' => $signaling_category]);
            } else {
                (new Socket())->emit('/administrator', 'signaling-category-update', ['signaling_category' => $signaling_category]);
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

        (new Socket())->emit('/administrator', 'signaling-category-delete', ['signaling_category' => $entity]);
    }
}
