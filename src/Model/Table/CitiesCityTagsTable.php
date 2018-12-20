<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CitiesCityTags Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\CityTagsTable|\Cake\ORM\Association\BelongsTo $CityTags
 *
 * @method \App\Model\Entity\CitiesCityTag get($primaryKey, $options = [])
 * @method \App\Model\Entity\CitiesCityTag newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CitiesCityTag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CitiesCityTag|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CitiesCityTag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CitiesCityTag[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CitiesCityTag findOrCreate($search, callable $callback = null, $options = [])
 */
class CitiesCityTagsTable extends Table
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

        $this->setTable('cities_city_tags');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->belongsTo('CityTags', [
            'foreignKey' => 'city_tag_id',

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
        $rules->add($rules->existsIn(['city_id'], 'Cities'));
        $rules->add($rules->existsIn(['city_tag_id'], 'CityTags'));

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

        $cities_city_tag = $this->find()->where(['CitiesCityTags.id' => $entity->id])->first();
        if ($cities_city_tag) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'cities-city-tag-create', ['cities_city_tag' => $cities_city_tag]);
            } else {
                (new Socket())->emit('/administrator', 'cities-city-tag-update', ['cities_city_tag' => $cities_city_tag]);
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

        (new Socket())->emit('/administrator', 'cities-city-tag-delete', ['cities_city_tag' => $entity]);
    }

}
