<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * Signaling Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property bool $archived
 * @property string $title
 * @property string $description
 * @property string $mayor_comment
 * @property int $signaling_category_id
 * @property int $status
 * @property string $street_number
 * @property string $route
 * @property string $postal_code
 * @property string $locality
 * @property string $country
 * @property string $country_short
 * @property float $lat
 * @property float $lng
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\SignalingCategory $signaling_category
 */
class Signaling extends Entity
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

    protected $_virtual = ['status_to_text'];


    protected function _getStatus_to_text()
    {
        return Options::getSignalingStatusOptions($this->status);

    }

    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setDescription($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setMayor_comment($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
