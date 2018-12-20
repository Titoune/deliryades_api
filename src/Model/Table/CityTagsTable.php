<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CityTagsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('city_tags');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');


        $this->belongsToMany('Cities');

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
            ->notEmpty('name', 'Ce champ est requis')
            ->lengthBetween('name', [2, 255], 'Ce champ doit contenir entre 2 et 255 caractères');
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

        $city_tag = $this->find()->where(['CityTags.id' => $entity->id])->first();
        if ($city_tag) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'city-tag-create', ['city_tag' => $city_tag]);
            } else {
                (new Socket())->emit('/administrator', 'city-tag-update', ['city_tag' => $city_tag]);
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

        (new Socket())->emit('/administrator', 'city-tag-delete', ['city_tag' => $entity]);
    }


}
