<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Newsgroups Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\NewsgroupCommentsTable|\Cake\ORM\Association\HasMany $NewsgroupComments
 *
 * @method \App\Model\Entity\Newsgroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\Newsgroup newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Newsgroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Newsgroup|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Newsgroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Newsgroup[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Newsgroup findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class NewsgroupsTable extends Table
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

        $this->setTable('newsgroups');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
        $this->addBehavior('CounterCache', ['Cities' => ['newsgroup_count']]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->hasMany('NewsgroupComments', [
            'foreignKey' => 'newsgroup_id']);
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
            ->lengthBetween('title', [2, 255], "Le champ doit contenir entre 2 et 255 caractères");

        $validator
            ->scalar('description')
            ->notEmpty('description', 'Ce champ est requis')
            ->lengthBetween('description', [2, 5000], 'Le champ doit contenir entre 2 et 5000 caractères');

        $validator
            ->boolean('activated')
            ->allowEmpty('activated');

        $validator
            ->boolean('closed')
            ->allowEmpty('closed');

        $validator
            ->boolean('open_comment')
            ->allowEmpty('open_comment');


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

        $this->hasOne('NewsgroupComments', [
            'className' => 'NewsgroupComments',
            'sort' => [
                'created' => 'desc'
            ],
            'strategy' => 'select'
        ])->setConditions(['NewsgroupComments.deleted IS ' => null]);

        $newsgroup = $this->find()->contain(['NewsgroupComments.Users'])->where(['Newsgroups.id' => $entity->id])->first();
        if ($newsgroup) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'newsgroup-create', ['newsgroup' => $newsgroup]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'newsgroup-update', ['newsgroup' => $newsgroup]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'newsgroup-delete', ['newsgroup' => $entity]);
    }


}
