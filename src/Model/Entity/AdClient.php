<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AdClient Entity
 *
 * @property int $id
 * @property string $company
 * @property string $email
 * @property string $phone
 * @property string $firstname
 * @property string $lastname
 * @property string $legal_form
 * @property string $identification_number
 * @property string $website
 * @property string $street_number
 * @property string $route
 * @property string $postal_code
 * @property string $locality
 * @property string $country
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $ad_count
 * @property int $exchange_count
 * @property int $invoice_count
 *
 * @property \App\Model\Entity\AdExchange[] $ad_exchanges
 * @property \App\Model\Entity\AdInvoice[] $ad_invoices
 * @property \App\Model\Entity\Ad[] $ads
 */
class AdClient extends Entity
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

    protected function _getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    protected function _setFirstname($val)
    {
        return (!empty($val)) ? ucfirst(mb_strtolower(htmlspecialchars(trim($val)), 'UTF-8')) : null;
    }

    protected function _setLastname($val)
    {
        return (!empty($val)) ? ucfirst(mb_strtolower(htmlspecialchars(trim($val)), 'UTF-8')) : null;
    }

    protected function _setEmail($val)
    {
        return (!empty($val)) ? mb_strtolower(htmlspecialchars(trim($val)), 'UTF-8') : null;
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

    protected function _setCompany($val)
    {
        return (!empty($val)) ? ucfirst(htmlspecialchars(trim($val))) : null;
    }

    protected function _setWebsite($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }

    protected function _setLegal_form($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }

    protected function _setIdentification_number($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }
}

?>