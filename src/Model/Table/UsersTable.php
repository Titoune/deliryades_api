<?php

namespace App\Model\Table;

use App\Utility\Socket;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\MayorsTable|\Cake\ORM\Association\BelongsTo $Mayors
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\AdministratorsTable|\Cake\ORM\Association\BelongsTo $Administrators
 * @property \App\Model\Table\AdClicksTable|\Cake\ORM\Association\HasMany $AdClicks
 * @property \App\Model\Table\AdDisplaysTable|\Cake\ORM\Association\HasMany $AdDisplays
 * @property \App\Model\Table\AdminBookmarksTable|\Cake\ORM\Association\HasMany $AdminBookmarks
 * @property \App\Model\Table\AdminExchangesTable|\Cake\ORM\Association\HasMany $AdminExchanges
 * @property \App\Model\Table\AdminFilesTable|\Cake\ORM\Association\HasMany $AdminFiles
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\HasMany $Cities
 * @property \App\Model\Table\CityRemindersTable|\Cake\ORM\Association\HasMany $CityReminders
 * @property \App\Model\Table\DiscussionUsersTable|\Cake\ORM\Association\HasMany $DiscussionUsers
 * @property \App\Model\Table\LogsTable|\Cake\ORM\Association\HasMany $Logs
 * @property \App\Model\Table\MandatariesTable|\Cake\ORM\Association\HasMany $Mandataries
 * @property \App\Model\Table\NewsgroupCommentsTable|\Cake\ORM\Association\HasMany $NewsgroupComments
 * @property \App\Model\Table\NotificationsTable|\Cake\ORM\Association\HasMany $Notifications
 * @property \App\Model\Table\PollAnswersTable|\Cake\ORM\Association\HasMany $PollAnswers
 * @property \App\Model\Table\PublicationCommentsTable|\Cake\ORM\Association\HasMany $PublicationComments
 * @property \App\Model\Table\PublicationLikesTable|\Cake\ORM\Association\HasMany $PublicationLikes
 * @property \App\Model\Table\PublicationsTable|\Cake\ORM\Association\HasMany $Publications
 * @property \App\Model\Table\ReportsTable|\Cake\ORM\Association\HasMany $Reports
 * @property \App\Model\Table\SignalingsTable|\Cake\ORM\Association\HasMany $Signalings
 * @property \App\Model\Table\SmsCampaignGroupUsersTable|\Cake\ORM\Association\HasMany $SmsCampaignGroupUsers
 * @property \App\Model\Table\SuggestionCommentsTable|\Cake\ORM\Association\HasMany $SuggestionComments
 * @property \App\Model\Table\SuggestionLikesTable|\Cake\ORM\Association\HasMany $SuggestionLikes
 * @property \App\Model\Table\SuggestionsTable|\Cake\ORM\Association\HasMany $Suggestions
 * @property \App\Model\Table\UserArchivesTable|\Cake\ORM\Association\HasMany $UserArchives
 * @property \App\Model\Table\UserCitiesTable|\Cake\ORM\Association\HasMany $UserCities
 * @property \App\Model\Table\UserCvsTable|\Cake\ORM\Association\HasMany $UserCvs
 * @property \App\Model\Table\UserItemPositionsTable|\Cake\ORM\Association\HasMany $UserItemPositions
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->hasMany('AdminBookmarks', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('AdminFiles', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('Administrators', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('Cities', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('ContractRenewals', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('DiscussionMessages', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('DiscussionUsers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserExclusions', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Logs', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('LoginAttempts', [
            'foreignKey' => 'login',
            'bindingKey' => 'email'
        ]);
        $this->hasMany('Mandataries', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('Mayors', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasOne('UserArchives', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasOne('Cities', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('NewsgroupComments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Notifications', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('PublicationComments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('PublicationLikes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Publications', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Reports', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('AdminExchanges', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Signalings', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SmsCampaignNotRecipients', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SmsCampaignOnlyRecipients', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SmsCampaignGroupUsers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SuggestionComments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SuggestionLikes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Suggestions', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('PollAnswers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserCities', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('UserCvs', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('UserItemPositions');

    }

    public function validationRegistration(Validator $validator)
    {
        $validator = $this->validationDefault($validator);
        $validator->requirePresence([
            'firstname',
            'lastname',
            'email',
            'presentation',
            'password1',
            //'is_website_terms_of_use_accepted'
        ]);
        return $validator;
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
            ->scalar('firstname')
            ->notEmpty('firstname', 'Ce champ est requis')
            ->lengthBetween('firstname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->add('firstname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->scalar('lastname')
            ->notEmpty('lastname', 'Ce champ est requis')
            ->lengthBetween('lastname', [2, 60], 'Le champ doit contenir entre 2 et 60 caractères')
            ->add('lastname', 'firstnameAlpha', [
                'rule' => ['custom', "/^[ a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð-]+$/u"],
                'message' => "Ce champ doit seulement contenir des caractères alphabétiques"
            ]);

        $validator
            ->notEmpty('email', 'Ce champ est requis')
            ->email('email', false, 'Le champ doit être une adresse email valide')
            ->add('email', [
                'emailUnique' => ['rule' => 'validateUnique', 'provider' => 'table',
                    'message' => "Cette adresse mail est déjà utilisée sur le site"
                ]
            ]);

        $validator
            ->email('email_notification', false, 'Le champ doit être une adresse email valide')
            ->maxLength('email_notification', 254)
            ->allowEmpty('email_notification');

        $validator
            ->scalar('password1')
            ->allowEmpty('password1', 'update')
            ->lengthBetween('password1', [2, 40], 'Le champ doit contenir entre 2 et 40 caractères');


        $validator
            ->scalar('password2')
            ->allowEmpty('password2', 'update')
            ->add('password2', [
                'password2Match' => [
                    'rule' => ['compareWith', 'password1'],
                    'message' => 'Les mots de passe ne correspondent pas',
                ]
            ]);

        $validator
            ->scalar('picture')
            ->maxLength('picture', 255)
            ->allowEmpty('picture');


        $validator
            ->date('birth', 'ymd', 'Le champ doit être une date valide')
            ->allowEmpty('birth')
            ->add('birth', 'birthOld', [
                'rule' => function ($value, $context) {

                    $year = (!isset($value['year'])) ? substr($value, 0, 4) : $value['year'];

                    if ($year <= date('Y') - 18) {
                        return true;
                    } else {
                        return false;
                    }
                },
                'message' => 'Vous devez être majeur pour utiliser le service'
            ]);


        $validator
            ->scalar('token')
            ->maxLength('token', 255)
            ->allowEmpty('token');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 1)
            ->notEmpty('sex', 'Ce champ est requis')
            ->inList('sex', ['m', 'f', 'i'], 'veuillez choisir une option dans la liste');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 45)
            ->add('phone', 'phoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de téléphone français sont acceptés"
            ])
            ->allowEmpty('phone');

        $validator
            ->scalar('cellphone')
            ->maxLength('cellphone', 45)
            ->add('cellphone', 'cellphoneFormat', [
                'rule' => ['custom', '/^(?:(?:\+|00)33|0)\s*[6-7](?:[\s.-]*\d{2}){4}$/'],
                'message' => "Seuls les numéros de mobile français sont acceptés"
            ])
            ->allowEmpty('cellphone');

        $validator
            ->notEmpty('is_terms_accepted')
            ->boolean('is_terms_accepted');

        $validator
            ->notEmpty('is_website_terms_of_use_accepted')
            ->boolean('is_website_terms_of_use_accepted');

        $validator
            ->notEmpty('is_personal_data_terms_of_use_accepted')
            ->boolean('is_personal_data_terms_of_use_accepted');

        $validator
            ->boolean('registered')
            ->allowEmpty('registered');

        $validator
            ->boolean('activated')
            ->notEmpty('activated');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->boolean('no_notification_email')
            ->allowEmpty('no_notification_email');

        $validator
            ->boolean('notification_sms')
            ->allowEmpty('notification_sms');

        $validator
            ->boolean('newsletter')
            ->notEmpty('newsletter');

        $validator
            ->scalar('autoconnect_type')
            ->maxLength('autoconnect_type', 30)
            ->allowEmpty('autoconnect_type');

        $validator
            ->allowEmpty('presentation');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }


// FINDER D'AUTHENTIFICATION
    public function findAuth(\Cake\ORM\Query $query, array $options)
    {
        return $query;
    }

    public function findFullname(\Cake\ORM\Query $query, array $options)
    {
        $fullname = trim($options['fullname']);
        $query->where(
            function ($exp, $q) use ($fullname) {
                $conc = $q->func()->concat([
                    'Users.firstname' => 'literal',
                    ' ',
                    'Users.lastname' => 'literal']);
                return $exp
                    ->like($conc, "%$fullname%");
            }
        );
        return $query;
    }

    public function findMandatary(\Cake\ORM\Query $query, array $options)
    {
        $query->contain(['Mandataries'])->where(
            ['Users.registered' => 1]
        )->matching('Mandataries', function ($q) {
            return $q->where(['Mandataries.activated' => 1, 'Mandataries.deleted is' => Null]);
        });
        return $query;
    }

    public function findCitizen(\Cake\ORM\Query $query, array $options)
    {
        $query->where(['Users.registered' => 1, 'Users.deleted IS ' => null, 'Users.activated' => 1]);
        return $query;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->uniqid) {
            $entity->uniqid = Tools::_getRandomHash();
            $entity->token = Tools::_getRandomHash();
        }
    }

    public function afterSave($event, $entity, $options)
    {
        //(new Firestore())->insert($this->getTable(), $entity->uniqid, $entity->toArray());

        $user = $this->find()->where(['Users.id' => $entity->id])->first();
        if ($user) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'user-create', ['user' => $user]);
            } else {
                (new Socket())->emit('/administrator', 'user-update', ['user' => $user]);
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

        (new Socket())->emit('/administrator', 'user-delete', ['user' => $entity]);
    }

}
