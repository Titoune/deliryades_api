<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * SuggestionLikes Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SuggestionCommentsTable|\Cake\ORM\Association\BelongsTo $SuggestionComments
 *
 * @method \App\Model\Entity\SuggestionLike get($primaryKey, $options = [])
 * @method \App\Model\Entity\SuggestionLike newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SuggestionLike[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionLike|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SuggestionLike patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionLike[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SuggestionLike findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SuggestionLikesTable extends Table
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

        $this->setTable('suggestion_likes');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'SuggestionComments' => ['up_count' => ['conditions' => ['val' => 1]], 'down_count' => ['conditions' => ['val' => 2]]]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('SuggestionComments', [
            'foreignKey' => 'suggestion_comment_id',

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
            ->allowEmpty('val');

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
        $rules->add($rules->existsIn(['suggestion_comment_id'], 'SuggestionComments'));

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

        $suggestion_comment = $this->SuggestionComments->find()->contain(['Suggestions', 'Users'])->where(['SuggestionComments.id' => $entity->suggestion_comment_id])->first();
        if ($suggestion_comment) {
            (new Socket())->emit('/dynamic-' . $suggestion_comment->suggestion->city_id, 'suggestion-comment-update', ['suggestion_comment' => $suggestion_comment]);
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
            (new Socket())->emit('/dynamic-' . $suggestion->city_id, 'suggestion-like-delete', ['suggestion_like' => $entity]);
        }
    }

}
