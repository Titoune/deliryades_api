<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Signalings Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SignalingCategoriesTable|\Cake\ORM\Association\BelongsTo $SignalingCategories
 *
 * @method \App\Model\Entity\Signaling get($primaryKey, $options = [])
 * @method \App\Model\Entity\Signaling newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Signaling[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Signaling|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Signaling patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Signaling[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Signaling findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class SignalingsTable extends Table
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

        $this->setTable('signalings');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['signaling_count']]);


        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);

        $this->belongsTo('SignalingCategories', [
            'foreignKey' => 'signaling_category_id'
        ]);

        $this->hasMany('Pictures', [
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Pictures.model' => 'Signalings']);
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
            ->boolean('archived')
            ->allowEmpty('archived');

        $validator
            ->scalar('title')
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 255], 'Le titre doit contenir entre 2 et 250 caractères');

        $validator
            ->scalar('description')
            ->notEmpty('description', 'Ce champ est requis')
            ->lengthBetween('description', [2, 5000], 'Vous devez remplir la description de votre signalement (minimum 2 caractères)');

        $validator
            ->scalar('mayor_comment')
            ->lengthBetween('mayor_comment', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->allowEmpty('mayor_comment');

        $validator
            ->allowEmpty('status');

        $validator
            ->scalar('street_number')
            ->maxLength('street_number', 10)
            ->allowEmpty('street_number');

        $validator
            ->scalar('route')
            ->maxLength('route', 155)
            ->allowEmpty('route');

        $validator
            ->scalar('postal_code')
            ->maxLength('postal_code', 10)
            ->allowEmpty('postal_code');

        $validator
            ->scalar('locality')
            ->maxLength('locality', 155)
            ->allowEmpty('locality');

        $validator
            ->scalar('country')
            ->maxLength('country', 155)
            ->allowEmpty('country');

        $validator
            ->scalar('country_short')
            ->maxLength('country_short', 10)
            ->allowEmpty('country_short');

        $validator
            ->decimal('lat')
            ->allowEmpty('lat');

        $validator
            ->decimal('lng')
            ->allowEmpty('lng');

        $validator
            ->boolean('cron_in_progress')
            ->allowEmpty('cron_in_progress');

        $validator
            ->boolean('notified')
            ->allowEmpty('notified');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['signaling_category_id'], 'SignalingCategories'));

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

        $signaling = $this->find()->contain(['Users', 'SignalingCategories', 'Pictures' => ['sort' => ['position' => 'asc']]])->where(['Signalings.id' => $entity->id])->first();
        if ($signaling) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'signaling-create', ['signaling' => $signaling]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'signaling-update', ['signaling' => $signaling]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'signaling-delete', ['signaling' => $entity]);
    }


}
