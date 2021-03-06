<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DiscussionComments Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\DiscussionComment get($primaryKey, $options = [])
 * @method \App\Model\Entity\DiscussionComment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DiscussionComment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionComment|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscussionComment|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscussionComment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionComment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DiscussionComment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DiscussionCommentsTable extends Table
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

        $this->setTable('discussion_comments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Senders', [
            'className' => 'Users',
            'foreignKey' => 'sender_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Receivers', [
            'className' => 'Users',
            'foreignKey' => 'receiver_id',
            'joinType' => 'INNER'
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
            ->notEmpty('content')
            ->lengthBetween('content', [2, 2000], 'Le champ doit contenir entre 2 et 2000 caractères');


        $validator
            ->dateTime('read_sender', 'ymd', null, 'Le champ doit être une date valide')
            ->allowEmpty('read_sender');

        $validator
            ->dateTime('read_receiver', 'ymd', null, 'Le champ doit être une date valide')
            ->allowEmpty('read_receiver');

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
        $rules->add($rules->existsIn(['sender_id'], 'Senders'));
        $rules->add($rules->existsIn(['receiver_id'], 'Receivers'));

        return $rules;
    }
}
