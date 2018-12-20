<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdTransfer Entity
 *
 * @property int $id
 * @property int $city_id
 * @property int $transfer_type
 * @property float $total
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\AdsCity[] $ads_cities
 */
class AdTransfer extends Entity
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
