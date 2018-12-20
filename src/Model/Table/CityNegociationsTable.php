<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * CityNegociations Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\CityNegociation get($primaryKey, $options = [])
 * @method \App\Model\Entity\CityNegociation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CityNegociation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CityNegociation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CityNegociation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CityNegociation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CityNegociation findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CityNegociationsTable extends Table
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

        $this->setTable('city_negociations');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['negociation_count']]);


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
            ->scalar('name')
            ->notEmpty('name', 'Ce champ est requis')
            ->lengthBetween('name', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères');

        $validator
            ->notEmpty('type');

        $validator
            ->scalar('description')
            ->lengthBetween('description', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->allowEmpty('description');

        $validator
            ->scalar('information')
            ->lengthBetween('information', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->allowEmpty('information');

        $validator
            ->scalar('incharge')
            ->lengthBetween('incharge', [2, 90], 'Ce champ doit contenir entre 2 et 90 caractères')
            ->notEmpty('incharge');


        $validator
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->allowEmpty('filename');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 45)
            ->add('phone', 'cellphoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros français sont acceptés"
            ])
            ->notEmpty('phone');

        $validator
            ->email('email', false, null, 'Le champ doit être une adresse email valide')
            ->notEmpty('email', 'Ce champ est requis');

        $validator
            ->decimal('price_initial')
            ->allowEmpty('price_initial');

        $validator
            ->decimal('price_final')
            ->allowEmpty('price_final');

        $validator
            ->allowEmpty('status');


        $validator->uploadedFile('file', [
            'types' => ["image/jpeg", "image/png", "image/gif", "application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "image/tiff"],
            'maxSize' => 8000000,
            'optional' => false
        ]);


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

        $city_negociation = $this->find()->where(['CityNegociations.id' => $entity->id])->first();
        if ($city_negociation) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-negociation-create', ['city_negociation' => $city_negociation]);

            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-negociation-update', ['city_negociation' => $city_negociation]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'city-negociation-delete', ['city_negociation' => $entity]);
    }


}
