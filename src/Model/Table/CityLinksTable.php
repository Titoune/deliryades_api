<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * CityLinks Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\CityLink get($primaryKey, $options = [])
 * @method \App\Model\Entity\CityLink newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CityLink[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CityLink|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CityLink patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CityLink[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CityLink findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CityLinksTable extends Table
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

        $this->setTable('city_links');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('Position');

        $this->addBehavior('CounterCache', [
            'Cities' => ['link_count']
        ]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

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
            ->scalar('title')
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 254], 'Ce champ doit contenir entre 2 et 254 caractères');

        $validator
            ->scalar('description')
            ->notEmpty('description', 'Ce champ est requis')
            ->lengthBetween('description', [2, 254], 'Ce champ doit contenir entre 2 et 254 caractères');

        $validator
            ->urlWithProtocol('url', 'Ce champ doit être une url valide')
            ->notEmpty('url', 'Ce champ est requis')
            ->maxLength('url', 255);

        $validator
            ->integer('position')
            ->allowEmpty('position');

        $validator
            ->boolean('activated')
            ->allowEmpty('activated');

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

        $city_link = $this->find()->where(['CityLinks.id' => $entity->id])->first();
        if ($city_link) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-link-create', ['city_link' => $city_link]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-link-update', ['city_link' => $city_link]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-link-delete', ['city_link' => $entity]);
    }


}
