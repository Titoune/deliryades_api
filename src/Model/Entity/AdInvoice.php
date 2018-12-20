<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdInvoice Entity
 *
 * @property int $id
 * @property int $ad_client_id
 * @property int $ad_id
 * @property int $num
 * @property string $company
 * @property string $name
 * @property float $total_ht
 * @property float $vat
 * @property float $total_vat
 * @property float $total_ttc
 * @property \Cake\I18n\FrozenTime $date_start
 * @property string $type
 * @property \Cake\I18n\FrozenTime $expiration_date
 * @property int $expiration_click
 * @property int $expiration_display
 * @property string $street_number
 * @property string $route
 * @property string $postal_code
 * @property \Cake\I18n\FrozenTime $date_paid
 * @property string $comment
 * @property bool $paid
 * @property string $locality
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\AdClient $ad_client
 * @property \App\Model\Entity\Ad $ad
 */
class AdInvoice extends Entity
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
