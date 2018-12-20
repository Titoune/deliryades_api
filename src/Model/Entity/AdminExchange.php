<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * AdminExchange Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property string $receiver
 * @property string $content
 * @property \Cake\I18n\FrozenTime $date
 * @property int $type
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\City $city
 */
class AdminExchange extends Entity
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

    protected $_virtual = ['type_to_text'];


    protected function _getTypeToText()
    {
        return Options::getExchangeTypeArray($this->type);
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}

