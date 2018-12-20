<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * SmsCampaignGroup Entity
 *
 * @property int $id
 * @property int $city_id
 * @property string $name
 * @property string $description
 * @property bool $open
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $sms_campaign_group_user_count
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\SmsCampaignGroupUser[] $sms_campaign_group_users
 * @property \App\Model\Entity\SmsCampaign[] $sms_campaigns
 */
class SmsCampaignGroup extends Entity
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

    protected $_virtual = ['sms_campaign_group_type_to_text'];


    protected function _setName($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setDescription($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getSmsCampaignGroupTypeToText()
    {
        return ($this->open == 0 ? "Fichier importé" : "Groupe ouvert");
    }
}
