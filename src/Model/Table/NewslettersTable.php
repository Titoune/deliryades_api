<?php
namespace App\Model\Table;

use App\Utility\Socket; use App\Utility\Firestore;
use App\Utility\Tools;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Newsletters Model
 *
 * @method \App\Model\Entity\Newsletter get($primaryKey, $options = [])
 * @method \App\Model\Entity\Newsletter newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Newsletter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Newsletter|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Newsletter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Newsletter[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Newsletter findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NewslettersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setTable('newsletters');

        $this->setPrimaryKey('id');

        //Behavior
        $this->addBehavior('Timestamp');
        $this->addBehavior('Log');
    }


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
            ->email('email', false, null, 'Le champ doit être une adresse email valide')
            ->add('email', [
                'emailUnique' => ['rule' => 'validateUnique', 'provider' => 'table',
                    'message' => "Cette adresse mail est déjà utilisée sur le site"
                ]
            ])
            ->notEmpty('email');

        $validator
            ->scalar('token')
            ->maxLength('token', 255)
            ->allowEmpty('token');

        return $validator;
    }


    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        return $rules;
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

        $newsletter = $this->find()->where(['Newsletters.id' => $entity->id])->first();
        if ($newsletter) {
            if ($entity->isNew()) {
                (new Socket())->emit('/administrator', 'newsletter-create', ['newsletter' => $newsletter]);
            } else {
                (new Socket())->emit('/administrator', 'newsletter-update', ['newsletter' => $newsletter]);
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

        (new Socket())->emit('/administrator', 'newsletter-delete', ['newsletter' => $entity]);
    }


}

?>
