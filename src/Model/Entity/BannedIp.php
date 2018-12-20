<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BannedIp Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $ip_address
 * @property \Cake\I18n\FrozenTime $created
 */
class BannedIp extends Entity
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
