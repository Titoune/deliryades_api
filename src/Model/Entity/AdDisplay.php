<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdDisplay Entity
 *
 * @property int $id
 * @property int $ad_id
 * @property int $ads_city_id
 * @property int $user_id
 * @property string $ip
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Ad $ad
 * @property \App\Model\Entity\AdsCity $ads_city
 * @property \App\Model\Entity\User $user
 */
class AdDisplay extends Entity
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
