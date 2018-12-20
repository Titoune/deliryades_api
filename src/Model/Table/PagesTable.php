<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * Pages Model
 *
 * @method \App\Model\Entity\Page get($primaryKey, $options = [])
 * @method \App\Model\Entity\Page newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Page[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Page|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Page patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Page[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Page findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagesTable extends Table
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

        $this->setTable('pages');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
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
            ->scalar('name')
            ->maxLength('name', 155)
            ->allowEmpty('name');

        $validator
            ->scalar('prefix')
            ->maxLength('prefix', 45)
            ->allowEmpty('prefix');

        $validator
            ->scalar('controller')
            ->maxLength('controller', 45)
            ->allowEmpty('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 45)
            ->allowEmpty('action');

        $validator
            ->scalar('title1')
            ->maxLength('title1', 255)
            ->allowEmpty('title1');

        $validator
            ->scalar('title2')
            ->maxLength('title2', 255)
            ->allowEmpty('title2');

        $validator
            ->scalar('title3')
            ->maxLength('title3', 255)
            ->allowEmpty('title3');

        $validator
            ->scalar('title4')
            ->maxLength('title4', 255)
            ->allowEmpty('title4');

        $validator
            ->scalar('title5')
            ->maxLength('title5', 255)
            ->allowEmpty('title5');

        $validator
            ->scalar('title6')
            ->maxLength('title6', 255)
            ->allowEmpty('title6');

        $validator
            ->scalar('title7')
            ->maxLength('title7', 255)
            ->allowEmpty('title7');

        $validator
            ->scalar('title8')
            ->maxLength('title8', 255)
            ->allowEmpty('title8');

        $validator
            ->scalar('title9')
            ->maxLength('title9', 255)
            ->allowEmpty('title9');

        $validator
            ->scalar('title10')
            ->maxLength('title10', 255)
            ->allowEmpty('title10');

        $validator
            ->scalar('content1')
            ->allowEmpty('content1');

        $validator
            ->scalar('content2')
            ->allowEmpty('content2');

        $validator
            ->scalar('content3')
            ->allowEmpty('content3');

        $validator
            ->scalar('content4')
            ->allowEmpty('content4');

        $validator
            ->scalar('content5')
            ->allowEmpty('content5');

        $validator
            ->scalar('content6')
            ->allowEmpty('content6');

        $validator
            ->scalar('content7')
            ->allowEmpty('content7');

        $validator
            ->scalar('content8')
            ->allowEmpty('content8');

        $validator
            ->scalar('content9')
            ->allowEmpty('content9');

        $validator
            ->scalar('content10')
            ->allowEmpty('content10');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
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

        $page = $this->find()->where(['Pages.id' => $entity->id])->first();
        if ($page) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'page-create', ['page' => $page]);
            } else {
                (new Socket())->emit('/administrator', 'page-update', ['page' => $page]);
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

        (new Socket())->emit('/administrator', 'page-delete', ['page' => $entity]);
    }
}
