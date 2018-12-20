<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cities Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DepartmentsTable|\Cake\ORM\Association\BelongsTo $Departments
 * @property \App\Model\Table\AdTransfersTable|\Cake\ORM\Association\HasMany $AdTransfers
 * @property \App\Model\Table\AdminExchangesTable|\Cake\ORM\Association\HasMany $AdminExchanges
 * @property \App\Model\Table\AlertsTable|\Cake\ORM\Association\HasMany $Alerts
 * @property \App\Model\Table\CityContactsTable|\Cake\ORM\Association\HasMany $CityContacts
 * @property \App\Model\Table\CityLinksTable|\Cake\ORM\Association\HasMany $CityLinks
 * @property \App\Model\Table\CityNegociationsTable|\Cake\ORM\Association\HasMany $CityNegociations
 * @property \App\Model\Table\CityPostalCodesTable|\Cake\ORM\Association\HasMany $CityPostalCodes
 * @property \App\Model\Table\CityRemindersTable|\Cake\ORM\Association\HasMany $CityReminders
 * @property \App\Model\Table\EventsTable|\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\LogsTable|\Cake\ORM\Association\HasMany $Logs
 * @property \App\Model\Table\NewsgroupsTable|\Cake\ORM\Association\HasMany $Newsgroups
 * @property \App\Model\Table\NotificationsTable|\Cake\ORM\Association\HasMany $Notifications
 * @property \App\Model\Table\PollsTable|\Cake\ORM\Association\HasMany $Polls
 * @property \App\Model\Table\PublicationsTable|\Cake\ORM\Association\HasMany $Publications
 * @property \App\Model\Table\SignalingsTable|\Cake\ORM\Association\HasMany $Signalings
 * @property \App\Model\Table\SmsCampaignGroupsTable|\Cake\ORM\Association\HasMany $SmsCampaignGroups
 * @property \App\Model\Table\SmsCampaignsTable|\Cake\ORM\Association\HasMany $SmsCampaigns
 * @property \App\Model\Table\SuggestionsTable|\Cake\ORM\Association\HasMany $Suggestions
 * @property \App\Model\Table\UserCitiesTable|\Cake\ORM\Association\HasMany $UserCities
 * @property \App\Model\Table\AdsTable|\Cake\ORM\Association\BelongsToMany $Ads
 *
 * @method \App\Model\Entity\City get($primaryKey, $options = [])
 * @method \App\Model\Entity\City newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\City[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\City|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\City patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\City[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\City findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class CitiesTable extends Table
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

        $this->setTable('cities');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->addBehavior('CounterCache', [
            'Departments' => ['city_count'],
            'Communities' => [
                'city_count' => ['conditions' => ['Cities.master' => 0, 'Cities.community_id IS NOT ' => null]]
            ]
        ]);

        $this->addBehavior('CounterCache', [

        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',

        ]);
        $this->hasMany('Alerts', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('ContractRenewals', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('CityLinks', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('CityContacts', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('CityReminders', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('AdsCities', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('CityNegociations', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('CityPostalCodes', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Exclusions', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Newsgroups', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Notifications', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Publications', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('AdminExchanges', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Signalings', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('SmsCampaigns', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Suggestions', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('Polls', [
            'foreignKey' => 'city_id'
        ]);
        $this->hasMany('UserCities', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('Mandataries', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasOne('Mayors', [
            'foreignKey' => 'city_id'
        ]);

        $this->belongsTo('CitySupposedMayors', ['foreignKey' => 'insee', 'bindingKey' => 'insee']);

        $this->belongsToMany('Ads');
        $this->hasMany('Pictures', [
                'foreignKey' => 'foreign_id',
                'conditions' => ['Pictures.model' => 'Cities']
            ]
        );

        $this->belongsToMany('PublicationDiffusions', [
            'foreignKey' => 'city_id',
            'targetForeignKey' => 'publication_diffusion_id',
            'joinTable' => 'cities_publication_diffusions'
        ]);

        $this->belongsTo('Communities', [
            'foreignKey' => 'community_id'
        ]);

        $this->hasMany('MunicipalCouncillorDirectoryEntries', [
            'foreignKey' => 'city_id'
        ]);

        $this->hasMany('MunicipalServiceDirectoryEntries', [
            'foreignKey' => 'city_id'
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

        $validator
            ->scalar('name')
            ->lengthBetween('name', [2, 255], 'Le champ doit contenir entre 2 et 255 caractères')
            ->notEmpty('name');

        $validator
            ->decimal('surface')
            ->notEmpty('surface')
            ->add('surface', 'checkdecimal', [
                'rule' => ['custom', "/^(?:999|\d{1,3})(?:[\,\.]\d{1,2})?$/"],
                'message' => 'Le champ doit être compris entre 0 et 999'
            ]);

        $validator
            ->integer('population')
            ->range('population', [1, 100000000], 'Le champ doit être compris entre 1 et 100000000')
            ->notEmpty('population');

        $validator
            ->decimal('density')
            ->add('density', 'checkdecimal', [
                'rule' => ['custom', "/^(?:100000|\d{1,6})(?:[\,\.]\d{1,2})?$/"],
                'message' => 'Le champ doit être compris entre 0 et 100000'
            ])
            ->notEmpty('density');

        $validator
            ->scalar('description')
            ->lengthBetween('description', [2, 50000], 'Le champ doit contenir entre 2 et 50000 caractères')
            ->allowEmpty('description');

        $validator
            ->scalar('picture')
            ->maxLength('picture', 255)
            ->allowEmpty('picture');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->allowEmpty('slug');

        $validator
            ->scalar('insee')
            ->notEmpty('insee')
            ->lengthBetween('insee', [5, 5], 'Le champ doit contenir 5 caractères')
            ->add('insee', [
                'inseeUnique' => ['rule' => 'validateUnique', 'provider' => 'table',
                    'message' => "Ce code insee est déjà utilisé dans la base"
                ]
            ]);

        $validator
            ->boolean('automatic_validation')
            ->allowEmpty('automatic_validation');

        $validator
            ->scalar('townhall_street')
            ->maxLength('townhall_street', 255)
            ->allowEmpty('townhall_street');

        $validator
            ->scalar('townhall_street2')
            ->maxLength('townhall_street2', 255)
            ->allowEmpty('townhall_street2');

        $validator
            ->scalar('townhall_postal_code')
            ->maxLength('townhall_postal_code', 10)
            ->allowEmpty('townhall_postal_code');

        $validator
            ->scalar('townhall_locality')
            ->maxLength('townhall_locality', 255)
            ->allowEmpty('townhall_locality');

        $validator
            ->scalar('townhall_phone')
            ->maxLength('townhall_phone', 45)
            ->allowEmpty('townhall_phone');

        $validator
            ->scalar('townhall_fax')
            ->maxLength('townhall_fax', 45)
            ->allowEmpty('townhall_fax');

        $validator
            ->email('townhall_email', false, 'Le champ doit être une adresse email valide')
            ->maxLength('townhall_email', 155)
            ->allowEmpty('townhall_email');

        $validator
            ->urlWithProtocol('townhall_website')
            ->maxLength('townhall_website', 255)
            ->allowEmpty('townhall_website');

        $validator
            ->scalar('townhall_siren')
            ->maxLength('townhall_siren', 255)
            ->allowEmpty('townhall_siren');

        $validator
            ->decimal('lat')
            ->allowEmpty('lat');

        $validator
            ->decimal('lng')
            ->allowEmpty('lng');

        $validator
            ->boolean('in_demo')
            ->allowEmpty('in_demo');

        $validator
            ->boolean('in_contract')
            ->allowEmpty('in_contract');

        $validator
            ->boolean('accept_ad')
            ->notEmpty('accept_ad');

        $validator
            ->email('signaling_email', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email', 250)
            ->allowEmpty('signaling_email');


        $validator
            ->email('signaling_email_cat1', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat1', 255)
            ->allowEmpty('signaling_email_cat1');

        $validator
            ->email('signaling_email_cat2', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat2', 255)
            ->allowEmpty('signaling_email_cat2');

        $validator
            ->email('signaling_email_cat3', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat3', 255)
            ->allowEmpty('signaling_email_cat3');

        $validator
            ->email('signaling_email_cat4', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat4', 255)
            ->allowEmpty('signaling_email_cat4');

        $validator
            ->email('signaling_email_cat5', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat5', 255)
            ->allowEmpty('signaling_email_cat5');

        $validator
            ->email('signaling_email_cat6', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat6', 255)
            ->allowEmpty('signaling_email_cat6');

        $validator
            ->email('signaling_email_cat7', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat7', 255)
            ->allowEmpty('signaling_email_cat7');

        $validator
            ->email('signaling_email_cat8', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat8', 255)
            ->allowEmpty('signaling_email_cat8');

        $validator
            ->email('signaling_email_cat9', false, 'Le champ doit être une adresse email valide')
            ->maxLength('signaling_email_cat9', 255)
            ->allowEmpty('signaling_email_cat9');

        $validator
            ->boolean('master');

        $validator
            ->allowEmpty('community_id')
            ->integer('community_id');

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
        $rules->add($rules->existsIn(['department_id'], 'Departments'));

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

        $city = $this->find()->where(['Cities.id' => $entity->id])->first();
        if ($city) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->id, 'city-create', ['city' => $city]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->id, 'city-update', ['city' => $city]);
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

        (new Socket())->emit('/dynamic-' . $entity->id, 'city-delete', ['city' => $entity]);
    }


}
