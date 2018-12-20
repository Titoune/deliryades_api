<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DirectoryPartnerCategories Model
 *
 * @property \App\Model\Table\DirectoryPartnersTable|\Cake\ORM\Association\BelongsToMany $DirectoryPartners
 *
 * @method \App\Model\Entity\DirectoryPartnerCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryPartnerCategory findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DirectoryPartnerCategoriesTable extends Table
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

        $this->setTable('directory_partner_categories');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsToMany('DirectoryPartners', [
            'foreignKey' => 'directory_partner_category_id',
            'targetForeignKey' => 'directory_partner_id',
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

        }
    }


    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $directory_partner_category = $this->find()->where(['DirectoryPartnerCategories.id' => $entity->id])->first();
        if ($directory_partner_category) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'directory-partner-category-create', ['directory_partner_category' => $directory_partner_category]);
            } else {
                (new Socket())->emit('/administrator', 'directory-partner-category-update', ['directory_partner_category' => $directory_partner_category]);
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

        (new Socket())->emit('/administrator', 'directory-partner-category-delete', ['directory_partner_category' => $entity]);
    }
}
