<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MunicipalServiceDirectoryEntriesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('municipal_service_directory_entries');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['municipal_service_directory_entry_count']]);

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
            ->notEmpty('name', 'Ce champ est requis')
            ->lengthBetween('name', [2, 255], "Le champ doit contenir entre 2 et 255 caractères");

        $validator
            ->allowEmpty('email')
            ->email('email', false, 'Le champ doit être une adresse email valide');

        $validator
            ->allowEmpty('email2')
            ->email('email2', false, 'Le champ doit être une adresse email valide');

        $validator
            ->allowEmpty('phone')
            ->add('phone', 'phoneFormat', [
                'rule' => ['custom', '/^(0|(\\+33)|0033)[1-9]{1}(([0-9]{2}){4})$/i'],
                'message' => "Seuls les numéros de téléphone français sont acceptés"
            ]);

        $validator
            ->allowEmpty('phone2')
            ->add('phone2', 'phoneFormat', [
                'rule' => ['custom', '/^(0|(\\+33)|0033)[1-9]{1}(([0-9]{2}){4})$/i'],
                'message' => "Seuls les numéros de téléphone français sont acceptés"
            ]);

        $validator
            ->allowEmpty('description')
            ->lengthBetween('description', [2, 800], 'Le champ doit contenir entre 2 et 800 caractères');

        $validator
            ->allowEmpty('opening_hours')
            ->lengthBetween('opening_hours', [2, 800], 'Le champ doit contenir entre 2 et 800 caractères');

        $validator
            ->allowEmpty('website')
            ->urlWithProtocol('website', 'le champ doit être une url valide');

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

        $municipal_service_directory_entry = $this->find()->where(['MunicipalServiceDirectoryEntries.id' => $entity->id])->first();
        if ($municipal_service_directory_entry) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'municipal-service-directory-entry-create', ['municipal_service_directory_entry' => $municipal_service_directory_entry]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'municipal-service-directory-entry-update', ['municipal_service_directory_entry' => $municipal_service_directory_entry]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'municipal-service-directory-entry-delete', ['municipal_service_directory_entry' => $entity]);
    }


}
