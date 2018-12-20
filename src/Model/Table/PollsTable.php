<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Polls Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\PollAnswersTable|\Cake\ORM\Association\HasMany $PollAnswers
 * @property \App\Model\Table\PollProposalsTable|\Cake\ORM\Association\HasMany $PollProposals
 *
 * @method \App\Model\Entity\Poll get($primaryKey, $options = [])
 * @method \App\Model\Entity\Poll newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Poll[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Poll|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Poll patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Poll[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Poll findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
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

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['poll_count']]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->hasMany('PollAnswers', [
            'foreignKey' => 'poll_id']);
        $this->hasMany('PollProposals', [
            'foreignKey' => 'poll_id']);
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
            ->notEmpty('title', 'Ce champ est requis')
            ->lengthBetween('title', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères');

        $validator
            ->scalar('question')
            ->notEmpty('question', 'Ce champ est requis')
            ->lengthBetween('question', [2, 200], 'Le champ doit contenir entre 2 et 200 caractères');

        $validator
            ->scalar('description')
            ->allowEmpty('description', 'Ce champ est requis')
            ->lengthBetween('description', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères');

        $validator
            ->dateTime('expiration', 'ymd', null, 'Le champ doit être une date valide')
            ->notEmpty('expiration', 'Ce champ est requis');

        $validator
            ->boolean('activated')
            ->allowEmpty('activated');


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

        $poll = $this->find()->contain(['PollProposals'])->where(['Polls.id' => $entity->id])->first();
        if ($poll) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'poll-create', ['poll' => $poll]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'poll-update', ['poll' => $poll]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'poll-delete', ['poll' => $entity]);
    }


}
