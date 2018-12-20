<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DirectoryPartners Model
 *
 * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\BelongsToMany $Departments
 * @property \App\Model\Table\DirectoryPartnerCategoriesTable|\Cake\ORM\Association\BelongsToMany $DirectoryPartnerCategories
 *
 * @method \App\Model\Entity\DirectoryPartner get($primaryKey, $options = [])
 * @method \App\Model\Entity\DirectoryPartner newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DirectoryPartner[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartner|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DirectoryPartner patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartner[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartner findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DirectoryPartnersTable extends Table
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

        $this->setTable('directory_partners');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsToMany('Departments', [
            'foreignKey' => 'directory_partner_id',
            'targetForeignKey' => 'department_id',
            'joinTable' => 'departments_directory_partners'
        ]);
        $this->belongsToMany('DirectoryPartnerCategories', [
            'foreignKey' => 'directory_partner_id',
            'targetForeignKey' => 'directory_partner_category_id',
            'joinTable' => 'directory_partner_categories_directory_partners'
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
            ->maxLength('name', 255)
            ->notEmpty('name');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 255)
            ->allowEmpty('phone');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->scalar('website')
            ->maxLength('website', 255)
            ->allowEmpty('website');

        $validator
            ->scalar('description')
            ->allowEmpty('description');

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

        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $directory_partner = $this->find()->where(['DirectoryPartners.id' => $entity->id])->first();
        if ($directory_partner) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'directory-partner-create', ['directory_partner' => $directory_partner]);
            } else {
                (new Socket())->emit('/administrator', 'directory-partner-update', ['directory_partner' => $directory_partner]);
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

        (new Socket())->emit('/administrator', 'directory-partner-delete', ['directory_partner' => $entity]);
    }


}
