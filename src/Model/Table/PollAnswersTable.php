<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PollAnswers Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PollProposalsTable|\Cake\ORM\Association\BelongsTo $PollProposals
 * @property \App\Model\Table\PollsTable|\Cake\ORM\Association\BelongsTo $Polls
 *
 * @method \App\Model\Entity\PollAnswer get($primaryKey, $options = [])
 * @method \App\Model\Entity\PollAnswer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PollAnswer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PollAnswer|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PollAnswer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PollAnswer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PollAnswer findOrCreate($search, callable $callback = null, $options = [])
 */
class PollAnswersTable extends Table
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

        $this->setTable('poll_answers');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Polls' => ['answer_count'],
            'PollProposals' => ['answer_count'],
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('PollProposals', [
            'foreignKey' => 'poll_proposal_id',

        ]);
        $this->belongsTo('Polls', [
            'foreignKey' => 'poll_id',

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
        $rules->add($rules->existsIn(['poll_proposal_id'], 'PollProposals'));
        $rules->add($rules->existsIn(['poll_id'], 'Polls'));

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

        $poll = $this->Polls->find()->contain(['PollProposals'])->where(['Polls.id' => $entity->poll_id])->first();
        if ($poll) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-update', ['poll' => $poll]);
                (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-answer-create', ['poll_answer' => $entity]);
            } else {
                (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-update', ['poll' => $poll]);
                (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-answer-update', ['poll_answer' => $entity]);
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

        $poll = $this->Polls->find()->contain(['PollProposals'])->where(['Polls.id' => $entity->poll_id])->first();
        if ($poll) {
            (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-update', ['poll' => $poll]);
            (new Socket())->emit('/dynamic-' . $poll->city_id, 'poll-answer-delete', ['poll_answer' => $entity]);

        }
    }


}
