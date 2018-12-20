<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CitySupposedMayors Model
 *
 * @method \App\Model\Entity\CitySupposedMayor get($primaryKey, $options = [])
 * @method \App\Model\Entity\CitySupposedMayor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CitySupposedMayor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CitySupposedMayor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CitySupposedMayor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CitySupposedMayor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CitySupposedMayor findOrCreate($search, callable $callback = null, $options = [])
 */
class CitySupposedMayorsTable extends Table
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

        $this->setTable('city_supposed_mayors');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->hasMany('Reports', [
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Reports.model' => 'city-supposed-mayor']);
        $this->hasOne('Cities', ['foreignKey' => 'insee', 'bindingKey' => 'insee']);
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
            ->scalar('lastname')
            ->maxLength('lastname', 155)
            ->notEmpty('lastname', 'Ce champ est requis')
            ->add('lastname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 155)
            ->notEmpty('firstname', 'Ce champ est requis')
            ->add('firstname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->email('email_pro', false, null, 'Le champ doit être une adresse email valide')
            ->maxLength('email_pro', 155)
            ->allowEmpty('email_pro');

        $validator
            ->email('email_perso', false, null, 'Le champ doit être une adresse email valide')
            ->maxLength('email_perso', 155)
            ->allowEmpty('email_perso');

        $validator
            ->scalar('phone_pro')
            ->maxLength('phone_pro', 45)
            ->allowEmpty('phone_pro');

        $validator
            ->scalar('phone_perso')
            ->maxLength('phone_perso', 45)
            ->allowEmpty('phone_perso');

        $validator
            ->scalar('picture')
            ->maxLength('picture', 254)
            ->allowEmpty('picture');

        $validator
            ->scalar('insee')
            ->maxLength('insee', 10)
            ->allowEmpty('insee');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 1)
            ->add('sex', 'custom', [
                'rule' => function ($value) {
                    return in_array($value, ['m', 'f', 'i']);
                },
                'message' => 'Veuillez choisir une valeur dans la liste'
            ])
            ->notEmpty('sex');

        $validator
            ->date('birth', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('birth');

        $validator
            ->boolean('no_email_notification')
            ->notEmpty('no_email_notification');

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

        $city_supposed_mayor = $this->find()->where(['CitySupposedMayors.id' => $entity->id])->first();
        if ($city_supposed_mayor) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'city-supposed-mayor-create', ['city_supposed_mayor' => $city_supposed_mayor]);
            } else {
                (new Socket())->emit('/administrator', 'city-supposed-mayor-update', ['city_supposed_mayor' => $city_supposed_mayor]);
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

        (new Socket())->emit('/administrator', 'city-supposed-mayor-delete', ['city_supposed_mayor' => $entity]);
    }
}
