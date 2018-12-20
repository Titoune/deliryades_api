<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Statistic Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property int $statistic_type
 * @property string $user_type
 * @property \Cake\I18n\FrozenTime $created
 * @property string $foreign_id
 * @property string $platform
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\Foreign $foreign
 */
class Statistic extends Entity
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
