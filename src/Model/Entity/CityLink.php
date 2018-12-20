<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CityLink Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property int $position
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\City $city
 */
class CityLink extends Entity
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

    protected function _setDescription($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setUrl($val)
    {
        return !empty($val) ? trim($val) : null;
    }


}
