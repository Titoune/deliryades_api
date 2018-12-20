<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * SmsCampaign Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property string $name
 * @property string $content
 * @property float $price_estimation_ht
 * @property float $price_ht
 * @property int $status
 * @property bool $invoiced
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $sms_count
 * @property bool $cron_in_progress
 * @property bool $notified
 * @property int $sms_campaign_type
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\SmsCampaignSm[] $sms_campaign_sms
 * @property \App\Model\Entity\SmsCampaignGroup[] $sms_campaign_groups
 */
class SmsCampaign extends Entity
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

    protected $_virtual = ['status_to_text', 'sms_campaign_type_to_text'];

    protected function _getStatusToText()
    {
        return Options::getSmscampaignStatusOptions($this->status);
    }

    protected function _getSmsCampaignTypeToText()
    {
        return Options::getSmscampaignTypesOptions($this->sms_campaign_type);
    }

    protected function _setName($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
