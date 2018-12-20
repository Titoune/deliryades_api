<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SmsCampaignSm Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $sms_campaign_id
 * @property string $cellphone
 * @property string $status
 * @property string $details
 * @property string $reference
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $firstname
 * @property string $lastname
 *
 * @property \App\Model\Entity\SmsCampaign $sms_campaign
 */
class SmsCampaignSm extends Entity
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

    protected function _setCellPhone($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getCellphone($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setStatus($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setDetails($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setReference($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
