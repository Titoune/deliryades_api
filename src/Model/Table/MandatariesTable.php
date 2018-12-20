<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Mandataries Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\MayorsTable|\Cake\ORM\Association\BelongsTo $Mayors
 *
 * @method \App\Model\Entity\Mandatary get($primaryKey, $options = [])
 * @method \App\Model\Entity\Mandatary newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Mandatary[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mandatary|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mandatary patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Mandatary[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mandatary findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class MandatariesTable extends Table
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

        $this->setTable('mandataries');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Users' => [
                'mandatary_count' => ['conditions' => ['Mandataries.deleted IS ' => null]]
            ],
            'Cities' => [
                'mandatary_count' => ['conditions' => ['Mandataries.deleted IS ' => null]]
            ]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',

        ]);
        $this->belongsTo('Mayors', [
            'foreignKey' => 'mayor_id',

        ]);

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);

        $this->hasMany('MandataryDevices');
        $this->hasMany('Notifications');

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
            ->boolean('activated')
            ->notEmpty('activated');

        $validator
            ->dateTime('logged')
            ->allowEmpty('logged');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->integer('permission_publication_comment_module')
            ->range('permission_publication_comment_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_publication_comment_module');

        $validator
            ->integer('permission_discussion_module')
            ->range('permission_discussion_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_discussion_module');

        $validator
            ->integer('permission_alert_module')
            ->range('permission_alert_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_alert_module');


        $validator
            ->integer('permission_newsgroup_module')
            ->range('permission_newsgroup_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_newsgroup_module');

        $validator
            ->integer('permission_newsgroup_comment_module')
            ->range('permission_newsgroup_comment_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_newsgroup_comment_module');

        $validator
            ->integer('permission_publication_module')
            ->range('permission_publication_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_publication_module');


        $validator
            ->integer('permission_event_module')
            ->range('permission_event_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_event_module');

        $validator
            ->integer('permission_city_module')
            ->range('permission_city_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_city_module');

        $validator
            ->integer('permission_poll_module')
            ->range('permission_poll_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_poll_module');

        $validator
            ->integer('permission_sms_campaign_module')
            ->range('permission_sms_campaign_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_sms_campaign_module');

        $validator
            ->integer('permission_mayor_module')
            ->range('permission_mayor_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_mayor_module');


        $validator
            ->integer('permission_signaling_module')
            ->range('permission_signaling_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_signaling_module');

        $validator
            ->integer('permission_city_negociation_module')
            ->range('permission_city_negociation_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_city_negociation_module');

        $validator
            ->integer('permission_suggestion_module')
            ->range('permission_suggestion_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_suggestion_module');

        $validator
            ->integer('permission_statistic_module')
            ->range('permission_statistic_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_statistic_module');

        $validator
            ->integer('permission_user_city_module')
            ->range('permission_user_city_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_user_city_module');

        $validator
            ->integer('permission_suggestion_comment_module')
            ->range('permission_suggestion_comment_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_suggestion_comment_module');


        $validator
            ->integer('permission_ad_module')
            ->range('permission_ad_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_ad_module');

        $validator
            ->integer('permission_sms_campaign_group_module')
            ->range('permission_sms_campaign_group_module', [1, 3], "Veuillez vérifier le champs")
            ->notEmpty('permission_sms_campaign_group_module');

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
        $rules->add($rules->existsIn(['mayor_id'], 'Mayors'));

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

        $mandatary = $this->find()->contain(['Users', 'Mayors'])->where(['Mandataries.id' => $entity->id])->first();
        if ($mandatary) {
            if ($entity->isNew()) {
                (new Socket())->emit('/perso-' . $mandatary->mayor->user_id, 'mandatary-create', ['mandatary' => $mandatary]);
            } else {
                (new Socket())->emit('/perso-' . $mandatary->mayor->user_id, 'mandatary-update', ['mandatary' => $mandatary]);
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

        $mandatary = $this->find()->contain(['Mayors'])->where(['Mandataries.id' => $entity->id])->first();
        if ($mandatary) {
            (new Socket())->emit('/perso-' . $mandatary->mayor->user_id, 'mandatary-delete', ['mandatary' => $entity]);
        }
    }
}
