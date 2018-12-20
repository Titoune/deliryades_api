<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserCv Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property string $title
 * @property float $year
 *
 * @property \App\Model\Entity\User $user
 */
class UserCv extends Entity
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

    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
