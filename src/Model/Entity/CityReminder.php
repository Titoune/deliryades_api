<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CityReminder Entity
 *
 * @property int $id
 * @property int $city_id
 * @property int $user_id
 * @property int $creator_id
 * @property int $closer_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $relance_date
 * @property string $description
 * @property \Cake\I18n\FrozenTime $done
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Creator $creator
 * @property \App\Model\Entity\Closer $closer
 */
class CityReminder extends Entity
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
