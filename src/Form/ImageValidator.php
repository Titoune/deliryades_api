<?php

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class ImageValidator extends Form
{

    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('type', 'string')
            ->addField('id', 'string')
            ->addField('format', ['type' => 'string'])
            ->addField('filename', ['type' => 'string']);
    }

    protected function _buildValidator(Validator $validator)
    {
        $validator
            ->requirePresence('type')
            ->notEmpty('type')
            ->add('type', 'custom', [
                'rule' => function ($value) {
                    return (in_array($value, ['ad', 'city', 'negociation', 'supposed-mayor', 'directory-partner', 'councillor', 'picture', 'publication', 'publication-diffusion', 'user', 'signaling', 'pdf']));
                }
            ]);

        $validator->requirePresence('id')
            ->notEmpty('id')
            ->integer('id')
            ->add('id', 'custom', [
                'rule' => function ($value) {
                    return $value >= 0;
                }
            ]);

        $validator->requirePresence('format')
            ->notEmpty('format')
            ->lengthBetween('format', [2, 10]);

        $validator->requirePresence('filename')
            ->notEmpty('filename')
            ->lengthBetween('filename', [5, 400])
            ->add('filename', 'custom', [
                'rule' => function ($value) {
                    return (in_array(pathinfo($value)['extension'], ['jpg', 'png']));
                }
            ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }


}

