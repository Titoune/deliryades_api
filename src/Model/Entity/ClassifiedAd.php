<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * ClassifiedAd Entity
 *
 * @property int $id
 * @property int $type
 * @property int $user_id
 * @property int $city_id
 * @property string $title
 * @property string $content
 * @property bool $cron_in_progress
 * @property bool $notified
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\City $city
 */
class ClassifiedAd extends Entity
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

    protected function _getType_to_text()
    {
        return Options::getClassifiedAdTypes($this->type);
    }

    protected function _setTitle($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setEmail($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setMayor_response($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }


    protected function _setPhone($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getPhone($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}