<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DepartmentsDirectoryPartners Model
 *
 * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\DirectoryPartnersTable|\Cake\ORM\Association\BelongsTo $DirectoryPartners
 *
 * @method \App\Model\Entity\DepartmentsDirectoryPartner get($primaryKey, $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DepartmentsDirectoryPartner findOrCreate($search, callable $callback = null, $options = [])
 */
class DepartmentsDirectoryPartnersTable extends Table
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

        $this->setTable('departments_directory_partners');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id'
        ]);
        $this->belongsTo('DirectoryPartners', [
            'foreignKey' => 'directory_partner_id'
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
        $rules->add($rules->existsIn(['directory_partner_id'], 'DirectoryPartners'));

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

        $departments_directory_partner = $this->find()->where(['DepartmentsDirectoryPartners.id' => $entity->id])->first();
        if ($departments_directory_partner) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'departments-directory-partner-create', ['departments_directory_partner' => $departments_directory_partner]);
            } else {
                (new Socket())->emit('/administrator', 'departments-directory-partner-update', ['departments_directory_partner' => $departments_directory_partner]);
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

        (new Socket())->emit('/administrator', 'departments-directory-partner-delete', ['departments_directory_partner' => $entity]);
    }


}
