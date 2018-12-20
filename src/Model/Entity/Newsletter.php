<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Newsletter Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $email
 * @property string $token
 * @property \Cake\I18n\FrozenTime $created
 */
class Newsletter extends Entity
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

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token'
    ];

    protected function _setEmail($val)
    {
        return (!empty($val)) ? strip_tags(mb_strtolower(trim($val), 'UTF-8')) : null;
    }

    protected function _setToken($val)
    {
        return (!empty($val)) ? $val : null;
    }
}

?>