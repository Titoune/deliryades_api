<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserCity Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property int $user_id
 * @property bool $activated
 * @property bool $validated
 * @property bool $refused
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logged
 * @property bool $current
 * @property bool $cron_in_progress
 * @property bool $notified
 * @property bool $exclusion_discussion_message
 * @property bool $exclusion_publication_comment
 * @property bool $exclusion_newsgroup_comment
 * @property \Cake\I18n\FrozenDate $exclusion_expiration
 * @property int $notification_count
 * @property int $notification_newsgroup
 * @property int $notification_newsgroup_comment
 * @property int $notification_publication
 * @property int $notification_publication_comment
 * @property int $notification_poll
 * @property int $notification_alert
 * @property int $notification_mayor
 * @property int $notification_suggestion
 * @property int $notification_suggestion_comment
 * @property int $notification_discussion_message
 * @property int $notification_publication_frequency
 * @property int $notification_publication_comment_frequency
 * @property int $notification_suggestion_frequency
 * @property int $notification_suggestion_comment_frequency
 * @property int $notification_newsgroup_frequency
 * @property int $notification_newsgroup_comment_frequency
 * @property int $notification_poll_frequency
 * @property int $notification_alert_frequency
 * @property int $notification_discussion_message_frequency
 * @property string $street_number
 * @property string $route
 * @property string $postal_code
 * @property string $locality
 * @property string $country
 * @property string $country_short
 * @property float $lat
 * @property float $lng
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\UserCityDevice[] $user_city_devices
 */
class UserCity extends Entity
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

    protected $_virtual = ['exclusion_expiration_to_date_fr'];

    protected function _getExclusionExpirationToDateFr()
    {
        if ($this->exclusion_expiration) {
            return $this->exclusion_expiration->i18nFormat('EEEE dd MMMM yyyy');
        } else {
            return null;
        }
    }

    protected function _setStreet_number($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setRoute($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setPostal_code($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setLocality($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setCountry($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setCountry_short($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setLat($val)
    {
        return (!empty($val)) ? $val : null;
    }

    protected function _setLng($val)
    {
        return (!empty($val)) ? $val : null;
    }

    protected function _setPresentation($val)
    {
        return (!empty($val)) ? ucfirst(strip_tags(trim($val))) : null;
    }
}
