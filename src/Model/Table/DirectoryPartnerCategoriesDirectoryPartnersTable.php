<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DirectoryPartnerCategoriesDirectoryPartners Model
 *
 * @property \App\Model\Table\DirectoryPartnersTable|\Cake\ORM\Association\BelongsTo $DirectoryPartners
 * @property \App\Model\Table\DirectoryPartnerCategoriesTable|\Cake\ORM\Association\BelongsTo $DirectoryPartnerCategories
 *
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner get($primaryKey, $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner findOrCreate($search, callable $callback = null, $options = [])
 */
class DirectoryPartnerCategoriesDirectoryPartnersTable extends Table
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

        $this->setTable('directory_partner_categories_directory_partners');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('DirectoryPartners', [
            'foreignKey' => 'directory_partner_id'
        ]);
        $this->belongsTo('DirectoryPartnerCategories', [
            'foreignKey' => 'directory_partner_category_id'
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
        $rules->add($rules->existsIn(['directory_partner_id'], 'DirectoryPartners'));
        $rules->add($rules->existsIn(['directory_partner_category_id'], 'DirectoryPartnerCategories'));

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

        $directory_partner_categories_directory_partner = $this->find()->where(['DirectoryPartnerCategoriesDirectoryPartners.id' => $entity->id])->first();
        if ($directory_partner_categories_directory_partner) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'directory-partner-categories-directory-partner-create', ['directory_partner_categories_directory_partner' => $directory_partner_categories_directory_partner]);
            } else {
                (new Socket())->emit('/administrator', 'directory-partner-categories-directory-partner-update', ['directory_partner_categories_directory_partner' => $directory_partner_categories_directory_partner]);
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

        (new Socket())->emit('/administrator', 'directory-partner-categories-directory-partner-delete', ['directory_partner_categories_directory_partner' => $entity]);
    }


}
