<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Administrator Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logged
 * @property \Cake\I18n\FrozenTime $deleted
 * @property bool $salesman
 *
 * @property \App\Model\Entity\User[] $users
 */
class Administrator extends Entity
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
