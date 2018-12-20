<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * Suggestions Model
 *
 * @property \App\Model\Table\SuggestionCategoriesTable|\Cake\ORM\Association\BelongsTo $SuggestionCategories
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\SuggestionCommentsTable|\Cake\ORM\Association\HasMany $SuggestionComments
 *
 * @method \App\Model\Entity\Suggestion get($primaryKey, $options = [])
 * @method \App\Model\Entity\Suggestion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Suggestion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Suggestion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Suggestion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Suggestion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Suggestion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class SuggestionsTable extends Table
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

        $this->setTable('suggestions');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', [
            'SuggestionCategories' => ['suggestion_count'],
            'Cities' => ['suggestion_count']
        ]);

        $this->belongsTo('SuggestionCategories', [
            'foreignKey' => 'suggestion_category_id',

        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->hasMany('SuggestionComments', [
            'foreignKey' => 'suggestion_id'
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
            ->maxLength('title', 255)
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 200], 'Le champ doit contenir entre 2 et 200 caractères');

        $validator
            ->scalar('content')
            ->notEmpty('content', 'Ce champ est requis')
            ->lengthBetween('content', [2, 1000], 'Le champ doit contenir entre 2 et 1000 caractères');

        $validator
            ->integer('status')
            ->range('status', [1, 5], 'Veuillez choisir un status dans la liste')
            ->notEmpty('status', 'Ce champ est requis');

        $validator
            ->boolean('engagement')
            ->allowEmpty('engagement');

        $validator
            ->integer('engagement_percentage')
            ->range('engagement_percentage', [50, 100], 'Veuillez choisir un pourcentage dans la liste')
            ->allowEmpty('engagement_percentage');

        $validator
            ->integer('engagement_number')
            ->range('engagement_number', [1, 10000000], 'Le champs doit être compris entre 1 et 10 000 000')
            ->allowEmpty('engagement_number');

        $validator
            ->dateTime('closed')
            ->allowEmpty('closed');

        $validator
            ->scalar('mayor_comment')
            ->lengthBetween('mayor_comment', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->allowEmpty('mayor_comment');

        $validator
            ->scalar('mayor_response')
            ->lengthBetween('mayor_response', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères')
            ->allowEmpty('mayor_response');


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
        $rules->add($rules->existsIn(['suggestion_category_id'], 'SuggestionCategories'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
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

        $suggestion = $this->find()->contain(['Users', 'SuggestionCategories'])->where(['Suggestions.id' => $entity->id])->first();
        if ($suggestion) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'suggestion-create', ['suggestion' => $suggestion]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'suggestion-update', ['suggestion' => $suggestion]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'suggestion-delete', ['suggestion' => $entity]);
    }


}
