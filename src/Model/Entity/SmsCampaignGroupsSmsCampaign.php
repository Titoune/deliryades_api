<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SmsCampaignGroupsSmsCampaign Entity
 *
 * @property int $id
 * @property int $sms_campaign_group_id
 * @property int $sms_campaign_id
 *
 * @property \App\Model\Entity\SmsCampaignGroup $sms_campaign_group
 * @property \App\Model\Entity\SmsCampaign $sms_campaign
 */
class SmsCampaignGroupsSmsCampaign extends Entity
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
