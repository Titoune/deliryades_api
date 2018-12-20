<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserArchive Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $email
 * @property string $email_notification
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property \Cake\I18n\FrozenDate $birth
 * @property string $cellphone
 * @property string $presentation
 * @property string $sex
 *
 * @property \App\Model\Entity\User $user
 */
class UserArchive extends Entity
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
