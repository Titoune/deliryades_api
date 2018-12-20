<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Polls Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PollAnswersTable|\Cake\ORM\Association\HasMany $PollAnswers
 * @property \App\Model\Table\PollProposalsTable|\Cake\ORM\Association\HasMany $PollProposals
 *
 * @method \App\Model\Entity\Poll get($primaryKey, $options = [])
 * @method \App\Model\Entity\Poll newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Poll[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Poll|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Poll|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Poll patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Poll[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Poll findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PollsTable extends Table
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

        $this->setTable('polls');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('PollAnswers', [
            'foreignKey' => 'poll_id'
        ]);
        $this->hasMany('PollProposals', [
            'foreignKey' => 'poll_id'
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
            ->scalar('question')
            ->lengthBetween('question', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères')
            ->notEmpty('question');

        $validator
            ->scalar('content')
            ->notEmpty('content')
            ->lengthBetween('content', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères');


        $validator
            ->dateTime('expiration', 'ymd', null, 'Le champ doit être une date valide')
            ->notEmpty('expiration');

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

        return $rules;
    }
}
