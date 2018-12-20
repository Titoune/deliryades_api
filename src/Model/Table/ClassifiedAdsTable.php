<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassifiedAds Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 *
 * @method \App\Model\Entity\ClassifiedAd get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassifiedAd newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClassifiedAd[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassifiedAd|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassifiedAd patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassifiedAd[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassifiedAd findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ClassifiedAdsTable extends Table
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

        $this->setTable('classified_ads');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['classified_ad_count' => ['conditions' => ['ClassifiedAds.deleted IS' => null]]]]);


        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);

        $this->hasMany('Reports', [
            'dependent' => true,
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Reports.model' => 'classified-ad']);
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
            ->integer('type')
            ->inList('type', [1, 2], 'veuillez choisir une option dans la liste')
            ->notEmpty('type');


        $validator
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 254], 'Le champ doit contenir entre 2 et 254 caractères');


        $validator
            ->notEmpty('content', 'Ce champ est requis')
            ->lengthBetween('content', [2, 2000], 'Le champ doit contenir entre 2 et 2000 caractères');

        $validator
            ->notEmpty('email', 'Au moins un des champs email ou téléphone est obligatoire', function ($context) {
                return (empty($context['data']['phone']));
            })
            ->email('email', false, 'Le champ doit être une adresse email valide');

        $validator
            ->allowEmpty('phone')
            ->add('phone', 'phoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de téléphone français sont acceptés"
            ]);

        $validator
            ->notEmpty('cgu');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['city_id'], 'Cities'));

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

        $classified_ad = $this->find()->contain(['Users'])->where(['ClassifiedAds.id' => $entity->id])->first();
        if ($classified_ad) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'classified-ad-create', ['classified_ad' => $classified_ad]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'classified-ad-update', ['classified_ad' => $classified_ad]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'classified-ad-delete', ['classified_ad' => $entity]);
    }


}