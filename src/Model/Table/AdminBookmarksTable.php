<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminBookmarks Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\AdminBookmark get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdminBookmark newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdminBookmark[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdminBookmark|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdminBookmark patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdminBookmark[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdminBookmark findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdminBookmarksTable extends Table
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

        $this->setTable('admin_bookmarks');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);

        $this->belongsTo('Comments', [
            'foreignKey' => 'foreign_id',

        ])->setConditions(['model' => 'Comments']);

        $this->belongsTo('NewsgroupComments', [
            'foreignKey' => 'foreign_id',

        ])->setConditions(['model' => 'NewsgroupComments']);

        $this->belongsTo('CitySupposedMayors', [
            'foreignKey' => 'foreign_id',

        ])->setConditions(['model' => 'CitySupposedMayors']);

        $this->belongsTo('NewsgroupComments', [
            'foreignKey' => 'foreign_id',

        ])->setConditions(['model' => 'NewsgroupComments']);
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
            ->maxLength('title', 200)
            ->allowEmpty('title');

        $validator
            ->scalar('model')
            ->maxLength('model', 90)
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
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

        $admin_bookmark = $this->find()->where(['AdminBookmarks.id' => $entity->id])->first();
        if ($admin_bookmark) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'admin-bookmark-create', ['admin_bookmark' => $admin_bookmark]);
            } else {
                (new Socket())->emit('/administrator', 'admin-bookmark-update', ['admin_bookmark' => $admin_bookmark]);
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

        (new Socket())->emit('/administrator', 'admin-bookmark-delete', ['admin_bookmark' => $entity]);
    }


}
