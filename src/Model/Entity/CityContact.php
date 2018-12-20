<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CityContact Entity
 *
 * @property int $id
 * @property int $city_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string $role
 * @property string $phone
 * @property string $cellphone
 * @property string $email
 * @property string $description
 *
 * @property \App\Model\Entity\City $city
 */
class CityContact extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];
}
