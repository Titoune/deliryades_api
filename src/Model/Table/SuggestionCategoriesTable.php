<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * SuggestionCategories Model
 *
 * @property \App\Model\Table\SuggestionsTable|\Cake\ORM\Association\HasMany $Suggestions
 *
 * @method \App\Model\Entity\SuggestionCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\SuggestionCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SuggestionCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionCategory|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SuggestionCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class SuggestionCategoriesTable extends Table
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

        $this->setTable('suggestion_categories');

        $this->setPrimaryKey('id');
        $this->addBehavior('Log');

        $this->hasMany('Suggestions', [
            'foreignKey' => 'suggestion_category_id'
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
            ->maxLength('name', 90)
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
            $entity->uniqid = Tools::_getRandomHash();
        }
    }


    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $suggestion_category = $this->find()->where(['SuggestionCategories.id' => $entity->id])->first();
        if ($suggestion_category) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'suggestion-category-create', ['suggestion_category' => $suggestion_category]);
            } else {
                (new Socket())->emit('/administrator', 'suggestion-category-update', ['suggestion_category' => $suggestion_category]);
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

        (new Socket())->emit('/administrator', 'suggestion-category-delete', ['suggestion_category' => $entity]);
    }
}
