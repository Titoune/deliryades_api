<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PollProposals Model
 *
 * @property \App\Model\Table\PollsTable|\Cake\ORM\Association\BelongsTo $Polls
 * @property \App\Model\Table\PollAnswersTable|\Cake\ORM\Association\HasMany $PollAnswers
 *
 * @method \App\Model\Entity\PollProposal get($primaryKey, $options = [])
 * @method \App\Model\Entity\PollProposal newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PollProposal[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PollProposal|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PollProposal|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PollProposal patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PollProposal[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PollProposal findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class PollProposalsTable extends Table
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

        $this->setTable('poll_proposals');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', [
            'Polls' => ['poll_proposal_count']
        ]);

        $this->belongsTo('Polls', [
            'foreignKey' => 'poll_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('PollAnswers', [
            'foreignKey' => 'poll_proposal_id'
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
            ->scalar('content')
            ->lengthBetween('content', [1, 250], 'Le champ doit contenir entre 1 et 250 caractères')
            ->notEmpty('content');

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
        $rules->add($rules->existsIn(['poll_id'], 'Polls'));

        return $rules;
    }
}
