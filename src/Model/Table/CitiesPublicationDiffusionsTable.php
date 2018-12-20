<?php

namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CitiesPublicationDiffusions Model
 *
 * @property \App\Model\Table\CitiesTable|\Cake\ORM\Association\BelongsTo $Cities
 * @property \App\Model\Table\PublicationDiffusionsTable|\Cake\ORM\Association\BelongsTo $PublicationDiffusions
 *
 * @method \App\Model\Entity\CitiesPublicationDiffusion get($primaryKey, $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CitiesPublicationDiffusion findOrCreate($search, callable $callback = null, $options = [])
 */
class CitiesPublicationDiffusionsTable extends Table
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

        $this->setTable('cities_publication_diffusions');

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');

        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',

        ]);
        $this->belongsTo('PublicationDiffusions', [
            'foreignKey' => 'publication_diffusion_id',

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
            ->boolean('accept')
            ->allowEmpty('accept');

        $validator
            ->boolean('refuse')
            ->allowEmpty('refuse');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

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
        $rules->add($rules->existsIn(['publication_diffusion_id'], 'PublicationDiffusions'));

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

        $cities_publication_diffusion = $this->find()->where(['CitiesPublicationDiffusions.id' => $entity->id])->first();
        if ($cities_publication_diffusion) {
            if ($entity->isNew()) {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'cities-publication-diffusion-create', ['cities_publication_diffusion' => $cities_publication_diffusion]);
            } else {
                (new Socket())->emit('/dynamic-' . $entity->city_id, 'cities-publication-diffusion-update', ['cities_publication_diffusion' => $cities_publication_diffusion]);
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

        (new Socket())->emit('/dynamic-' . $entity->city_id, 'cities-publication-diffusion-delete', ['cities_publication_diffusion' => $entity]);
    }

}
