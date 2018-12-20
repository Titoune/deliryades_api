<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdClients Model
 *
 * @property \App\Model\Table\AdExchangesTable|\Cake\ORM\Association\HasMany $AdExchanges
 * @property \App\Model\Table\AdInvoicesTable|\Cake\ORM\Association\HasMany $AdInvoices
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\HasMany $Ads
 *
 * @method \App\Model\Entity\AdClient get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdClient newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdClient[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdClient|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdClient patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdClient[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdClient findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdClientsTable extends Table
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

        $this->setTable('ad_clients');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->hasMany('Ads', [
            'foreignKey' => 'ad_client_id'
        ]);

        $this->hasMany('AdExchanges', [
            'foreignKey' => 'ad_client_id'
        ]);

        $this->hasMany('AdInvoices', [
            'foreignKey' => 'ad_client_id'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('company')
            ->lengthBetween('company', [2, 90], 'Le champ doit contenir entre 2 et 90 caractères');

        $validator
            ->notEmpty('email')
            ->email('email', false, null, 'Le champ doit être une adresse email valide')
            ->add('email', [
                'emailUnique' => ['rule' => 'validateUnique', 'provider' => 'table',
                    'message' => "Cette adresse mail est déja utilisée sur le site"
                ]
            ]);

        $validator
            ->scalar('phone')
            ->maxLength('phone', 50)
            ->allowEmpty('phone');

        $validator
            ->notEmpty('firstname')
            ->lengthBetween('firstname', [2, 45], 'Le champ doit contenir entre 2 et 45 caractères')
            ->add('firstname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->notEmpty('lastname')
            ->lengthBetween('lastname', [2, 45], 'Le champ doit contenir entre 2 et 45 caractères')
            ->add('lastname', 'lastnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->scalar('legal_form')
            ->maxLength('legal_form', 255)
            ->allowEmpty('legal_form');

        $validator
            ->scalar('identification_number')
            ->maxLength('identification_number', 255)
            ->allowEmpty('identification_number');

        $validator
            ->scalar('website')
            ->maxLength('website', 255)
            ->urlWithProtocol('website', 'le champ doit être une url valide')
            ->allowEmpty('website');

        $validator
            ->scalar('street_number')
            ->maxLength('street_number', 45)
            ->allowEmpty('street_number');

        $validator
            ->scalar('route')
            ->maxLength('route', 155)
            ->allowEmpty('route');

        $validator
            ->scalar('postal_code')
            ->maxLength('postal_code', 45)
            ->allowEmpty('postal_code');

        $validator
            ->scalar('locality')
            ->maxLength('locality', 155)
            ->allowEmpty('locality');

        $validator
            ->scalar('country')
            ->maxLength('country', 155)
            ->allowEmpty('country');

        return $validator;
    }


    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));

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

        $ad_client = $this->find()->where(['AdClients.id' => $entity->id])->first();
        if ($ad_client) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-client-create', ['ad_client' => $ad_client]);
            } else {
                (new Socket())->emit('/administrator', 'ad-client-update', ['ad_client' => $ad_client]);
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

        (new Socket())->emit('/administrator', 'ad-client-delete', ['ad_client' => $entity]);
    }
}
