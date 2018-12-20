<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * Pictures Model
 *
 *
 * @method \App\Model\Entity\Picture get($primaryKey, $options = [])
 * @method \App\Model\Entity\Picture newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Picture[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Picture|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Picture patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Picture[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Picture findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PicturesTable extends Table
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

        $this->setTable('pictures');

        $this->setPrimaryKey('id');

        //Behavior
        $this->addBehavior('Timestamp');
        $this->addBehavior('Position');
        $this->addBehavior('Log');

        //Relations
        $this->belongsTo('Cities', [
                'foreignKey' => 'foreign_id'
            ]
        );
        $this->belongsTo('Publications', [
                'foreignKey' => 'foreign_id'
            ]
        );
        $this->belongsTo('Signalings', [
                'foreignKey' => 'foreign_id'
            ]
        );
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
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->allowEmpty('filename');

        $validator
            ->integer('position')
            ->allowEmpty('position');

        $validator
            ->scalar('model')
            ->maxLength('model', 45)
            ->allowEmpty('model');

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

        $picture = $this->find()->where(['Pictures.id' => $entity->id])->first();
        if ($picture) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'picture-create', ['picture' => $picture]);
            } else {
                (new Socket())->emit('/administrator', 'picture-update', ['picture' => $picture]);
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

        (new Socket())->emit('/administrator', 'picture-delete', ['picture' => $entity]);
    }


}
