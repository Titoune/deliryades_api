<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SuggestionComments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SuggestionsTable|\Cake\ORM\Association\BelongsTo $Suggestions
 * @property \App\Model\Table\SuggestionLikesTable|\Cake\ORM\Association\HasMany $SuggestionLikes
 *
 * @method \App\Model\Entity\SuggestionComment get($primaryKey, $options = [])
 * @method \App\Model\Entity\SuggestionComment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SuggestionComment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionComment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SuggestionComment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionComment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionComment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SuggestionCommentsTable extends Table
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

        $this->setTable('suggestion_comments');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Suggestions' => ['vote_count', 'for_count' => ['conditions' => ['vote' => 1]], 'against_count' => ['conditions' => ['vote' => 2]], 'comment_count' => ['conditions' => ['content IS NOT' => null, 'deleted IS' => null]]]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Suggestions', [
            'foreignKey' => 'suggestion_id',

        ]);

        $this->hasMany('SuggestionLikes', [
            'foreignKey' => 'suggestion_id'
        ]);

        $this->hasMany('Reports', [
            'dependent' => true,
            'foreignKey' => 'foreign_id'
        ])->setConditions(['Reports.model' => 'suggestion-comment']);
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
            ->notEmpty('vote', 'Ce champ est requis')
            ->range('vote', [1, 2], "La valeur du champs est incorrecte");

        $validator
            ->scalar('content')
            ->lengthBetween('content', [2, 2000], "Le champs doit contenir entre 2 et 2000 caractères")
            ->allowEmpty('content');

        $validator
            ->boolean('anonymous')
            ->allowEmpty('anonymous');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');


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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['suggestion_id'], 'Suggestions'));

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

        $suggestion_comment = $this->find()->contain(['Users'])->where(['SuggestionComments.id' => $entity->id])->first();
        if ($suggestion_comment) {
            $suggestion = $this->Suggestions->find()->contain(['SuggestionCategories', 'Users'])->where(['Suggestions.id' => $entity->suggestion_id])->first();
            if ($suggestion) {
                (new Socket())->emit('/dynamic-' . $suggestion->city_id, 'suggestion-update', ['suggestion' => $suggestion]);
                if ($entity->isNew()) {
                    (new Socket())->emit('/dynamic-' . $suggestion->city_id, 'suggestion-comment-create', ['suggestion_comment' => $suggestion_comment]);
                } else {
                    (new Socket())->emit('/dynamic-' . $suggestion->city_id, 'suggestion-comment-update', ['suggestion_comment' => $suggestion_comment]);
                }
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

        $suggestion = $this->Suggestions->find()->where(['Suggestions.id' => $entity->suggestion_id])->first();
        if ($suggestion) {
            (new Socket())->emit('/dynamic-' . $suggestion->city_id, 'suggestion-comment-delete', ['suggestion_comment' => $entity]);
        }
    }
}
