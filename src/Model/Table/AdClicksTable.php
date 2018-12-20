<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdClicks Model
 *
 * @property \App\Model\Table\AdsCitiesTable|\Cake\ORM\Association\BelongsTo $AdsCities
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsTo $Ads
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\AdClick get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdClick newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdClick[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdClick|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdClick patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdClick[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdClick findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdClicksTable extends Table
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

        $this->setTable('ad_clicks');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('CounterCache', [
            'Ads' => ['click_count'],
            'AdsCities' => ['click_count'],
        ]);

        $this->belongsTo('AdsCities', [
            'foreignKey' => 'ads_city_id',

        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);

        $this->belongsTo('Ads', [
            'foreignKey' => 'ad_id',

        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('ip');

        return $validator;
    }


    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['ads_city_id'], 'AdsCities'));
        $rules->add($rules->existsIn(['ad_id'], 'Ads'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->uniqid) {

        }
    }


    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $ad_click = $this->find()->where(['AdClicks.id' => $entity->id])->first();
        if ($ad_click) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-click-create', ['ad_click' => $ad_click]);
            } else {
                (new Socket())->emit('/administrator', 'ad-click-update', ['ad_click' => $ad_click]);
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

        (new Socket())->emit('/administrator', 'ad-click-delete', ['ad_click' => $entity]);
    }
}
