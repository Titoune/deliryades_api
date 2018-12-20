<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdsCity Entity
 *
 * @property int $id
 * @property int $city_id
 * @property int $ad_id
 * @property int $ad_transfer_id
 * @property float $transfer_amount
 * @property int $coef
 * @property float $price_ht
 * @property float $rate_payment
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $daily_display_count
 * @property int $daily_click_count
 * @property int $city_citizen_start_count
 * @property int $city_citizen_end_count
 * @property int $click_count
 * @property int $display_count
 * @property \Cake\I18n\FrozenTime $reclamation_date
 * @property string $reclamation_content
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\Ad $ad
 * @property \App\Model\Entity\AdTransfer $ad_transfer
 * @property \App\Model\Entity\AdClick[] $ad_clicks
 * @property \App\Model\Entity\AdDisplay[] $ad_displays
 */
class AdsCity extends Entity
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
