<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * City Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property int $department_id
 * @property string $name
 * @property float $surface
 * @property int $population
 * @property float $density
 * @property string $description
 * @property string $picture
 * @property string $slug
 * @property string $insee
 * @property bool $automatic_validation
 * @property string $townhall_street
 * @property string $townhall_street2
 * @property string $townhall_postal_code
 * @property string $townhall_locality
 * @property string $townhall_phone
 * @property string $townhall_fax
 * @property string $townhall_email
 * @property string $townhall_website
 * @property string $townhall_siren
 * @property float $lat
 * @property float $lng
 * @property bool $accept_ad
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $signaling_email
 * @property int $publication_count
 * @property int $newsgroup_count
 * @property int $link_count
 * @property int $event_count
 * @property int $postal_code_count
 * @property int $poll_count
 * @property int $exchange_count
 * @property int $exclusion_count
 * @property int $picture_count
 * @property int $citizen_validated_count
 * @property int $citizen_unvalidated_count
 * @property int $citizen_refused_count
 * @property int $ad_count
 * @property string $signaling_email_cat1
 * @property string $signaling_email_cat2
 * @property string $signaling_email_cat3
 * @property string $signaling_email_cat4
 * @property string $signaling_email_cat5
 * @property string $signaling_email_cat6
 * @property string $signaling_email_cat7
 * @property string $signaling_email_cat8
 * @property string $signaling_email_cat9
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Department $department
 * @property \App\Model\Entity\AdTransfer[] $ad_transfers
 * @property \App\Model\Entity\AdminExchange[] $admin_exchanges
 * @property \App\Model\Entity\Alert[] $alerts
 * @property \App\Model\Entity\CityContact[] $city_contacts
 * @property \App\Model\Entity\CityLink[] $city_links
 * @property \App\Model\Entity\CityNegociation[] $city_negociations
 * @property \App\Model\Entity\CityPostalCode[] $city_postal_codes
 * @property \App\Model\Entity\CityReminder[] $city_reminders
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\Log[] $logs
 * @property \App\Model\Entity\Newsgroup[] $newsgroups
 * @property \App\Model\Entity\Notification[] $notifications
 * @property \App\Model\Entity\Poll[] $polls
 * @property \App\Model\Entity\Publication[] $publications
 * @property \App\Model\Entity\Signaling[] $signalings
 * @property \App\Model\Entity\SmsCampaignGroup[] $sms_campaign_groups
 * @property \App\Model\Entity\SmsCampaign[] $sms_campaigns
 * @property \App\Model\Entity\Suggestion[] $suggestions
 * @property \App\Model\Entity\UserCity[] $user_cities
 * @property \App\Model\Entity\Ad[] $ads
 */
class City extends Entity
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

    protected $_virtual = ['picture_sizes', 'account_type', 'account_user_type'];


    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'cities' . DS . $this->id . DS . $this->picture);
        } else {
            $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . 'default-city.jpg');
        }

        return [
            'xs' => $pic_url . "&width=300&height=150",
            'sm' => $pic_url . "&width=600&height=300",
            'md' => $pic_url . "&width=600&height=300",
            'lg' => $pic_url,
            'default' => $pic_url,
        ];

    }


    protected function _getAccountType()
    {
        return ($this->master == true ? "communauté de communes" : "commune");
    }

    protected function _getAccountUserType()
    {
        return ($this->master == true ? "président" : "maire");
    }

    protected function _setName($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setPicture($val)
    {
        return !empty($val) ? trim($val) : null;
    }


    protected function _setSlug($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setInsee($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_street($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_street2($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_postal_code($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_locality($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_phone($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_fax($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_email($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_website($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setTownhall_siren($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setLat($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setLng($val)
    {
        return !empty($val) ? trim($val) : null;
    }
}
