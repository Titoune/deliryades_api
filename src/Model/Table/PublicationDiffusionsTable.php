<?php

namespace App\Model\Table;

use App\Utility\Socket;
use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PublicationDiffusions Model
 *
 * @property \App\Model\Table\DiffuserUsersTable|\Cake\ORM\Association\BelongsTo $DiffuserUsers
 * @property \App\Model\Table\InitialPublicationsTable|\Cake\ORM\Association\BelongsTo $InitialPublications
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsToMany $Cities
 *
 * @method \App\Model\Entity\PublicationDiffusion get($primaryKey, $options = [])
 * @method \App\Model\Entity\PublicationDiffusion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PublicationDiffusion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PublicationDiffusion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PublicationDiffusion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PublicationDiffusion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PublicationDiffusion findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PublicationDiffusionsTable extends Table
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

        $this->setTable('publication_diffusions');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Users', [
            'foreignKey' => 'diffuser_user_id'
        ]);
        $this->belongsTo('Publications', [
            'foreignKey' => 'initial_publication_id',

        ]);

        $this->hasMany('CitiesPublicationDiffusions', [
            'foreignKey' => 'publication_diffusion_id'
        ]);

        $this->belongsToMany('Cities', [
            'foreignKey' => 'publication_diffusion_id',
            'targetForeignKey' => 'city_id',
            'joinTable' => 'cities_publication_diffusions'
        ]);

        $this->hasMany('Pictures', [
            'foreignKey' => 'foreign_id',
            'dependent' => true,
        ])->setConditions(['Pictures.model' => 'PublicationDiffusions']);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
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
        $rules->add($rules->existsIn(['diffuser_user_id'], 'Users'));
        $rules->add($rules->existsIn(['initial_publication_id'], 'Publications'));

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

        $publication_diffusion = $this->find()->where(['PublicationDiffusions.id' => $entity->id])->first();
        if ($publication_diffusion) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'publication-diffusion-create', ['publication_diffusion' => $publication_diffusion]);
            } else {
                (new Socket())->emit('/administrator', 'publication-diffusion-update', ['publication_diffusion' => $publication_diffusion]);
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

        (new Socket())->emit('/administrator', 'publication-diffusion-delete', ['publication_diffusion' => $entity]);
    }


}
