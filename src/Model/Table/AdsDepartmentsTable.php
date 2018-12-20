<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdsDepartmentsTable extends Table
{
    /**
     * AdsDepartments Model
     *
     * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\BelongsTo $Departments
     * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsTo $Ads
     *
     * @method \App\Model\Entity\AdsDepartment get($primaryKey, $options = [])
     * @method \App\Model\Entity\AdsDepartment newEntity($data = null, array $options = [])
     * @method \App\Model\Entity\AdsDepartment[] newEntities(array $data, array $options = [])
     * @method \App\Model\Entity\AdsDepartment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
     * @method \App\Model\Entity\AdsDepartment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
     * @method \App\Model\Entity\AdsDepartment[] patchEntities($entities, array $data, array $options = [])
     * @method \App\Model\Entity\AdsDepartment findOrCreate($search, callable $callback = null, $options = [])
     */


    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('ads_departments');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',

        ]);
        $this->belongsTo('Ads', [
            'foreignKey' => 'ad_id',

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
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['ad_id'], 'Ads'));

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

        $ads_department = $this->find()->where(['AdsDepartments.id' => $entity->id])->first();
        if ($ads_department) {


            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ads-department-create', ['ads_department' => $ads_department]);
            } else {
                (new Socket())->emit('/administrator', 'ads-department-update', ['ads_department' => $ads_department]);
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

        (new Socket())->emit('/administrator', 'ads-department-delete', ['ads_department' => $entity]);
    }


}
