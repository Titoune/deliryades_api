<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdDisplays Model
 *
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsTo $Ads
 * @property \App\Model\Table\AdsCitiesTable|\Cake\ORM\Association\BelongsTo $AdsCities
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\AdDisplay get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdDisplay newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdDisplay[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdDisplay|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdDisplay patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdDisplay[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdDisplay findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdDisplaysTable extends Table
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

        $this->setTable('ad_displays');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('CounterCache', [
            'Ads' => ['display_count'],
            'AdsCities' => ['display_count'],
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
            ->scalar('ip')
            ->maxLength('ip', 45)
            ->notEmpty('ip');

        return $validator;
    }


    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['ads_city_id'], 'AdsCities'));
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

        $ad_display = $this->find()->where(['AdDisplays.id' => $entity->id])->first();
        if ($ad_display) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'ad-display-create', ['ad_display' => $ad_display]);
            } else {
                (new Socket())->emit('/administrator', 'ad-display-update', ['ad_display' => $ad_display]);
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

        (new Socket())->emit('/administrator', 'ad-display-delete', ['ad_display' => $entity]);
    }


}
