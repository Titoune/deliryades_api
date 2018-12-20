<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

class Ad extends Entity
{
    /**
     * Ad Entity
     *
     * @property int $id
     * @property int $ad_client_id
     * @property string $name
     * @property string $title
     * @property string $content
     * @property string $picture
     * @property string $website
     * @property int $expiration_type
     * @property \Cake\I18n\FrozenTime $expiration_date
     * @property int $expiration_click
     * @property int $expiration_display
     * @property \Cake\I18n\FrozenTime $date_start
     * @property float $total_ht
     * @property float $vat
     * @property float $total_vat
     * @property float $total_ttc
     * @property float $price_date
     * @property float $price_click
     * @property float $price_display
     * @property \Cake\I18n\FrozenTime $created
     * @property \Cake\I18n\FrozenTime $modified
     * @property bool $in_progress
     * @property bool $activated
     * @property bool $closed
     * @property int $paid_invoice
     * @property int $invoice_count
     * @property int $click_count
     * @property int $city_count
     * @property int $display_count
     * @property int $type
     *
     * @property \App\Model\Entity\AdClient $ad_client
     * @property \App\Model\Entity\AdClick[] $ad_clicks
     * @property \App\Model\Entity\AdDisplay[] $ad_displays
     * @property \App\Model\Entity\AdInvoice[] $ad_invoices
     * @property \App\Model\Entity\City[] $cities
     * @property \App\Model\Entity\Department[] $departments
     */
    protected $_accessible = [
        '*' => true
    ];

    protected $_virtual = ['type_text', 'picture_sizes', 'expiration_type_to_text'];

    protected function _getExpirationTypeToText()
    {
        return Options::getExpirationtypes($this->expiration_type);
    }



    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'ads' . DS . $this->id . DS . $this->picture);
        } else {
            $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . 'default-ad.jpg');
        }

        return [
            'xs' => $pic_url . "&width=60&height=60",
            'sm' => $pic_url . "&width=60&height=60",
            'md' => $pic_url . "&width=100&height=100",
            'lg' => $pic_url . "&width=120&height=120",
            'default' => $pic_url,
        ];
    }

    protected function _getType_text()
    {
        if ($this->type == 2) {
            return "Sponsor Mécène";
        } else {
            return "Contenu sponsorisé";

        }
    }
}
