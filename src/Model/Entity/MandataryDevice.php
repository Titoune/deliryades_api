<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MandataryDevice Entity
 *
 * @property int $id
 * @property int $user_city_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $device_manufacturer
 * @property string $device_platform
 * @property string $device_model
 * @property string $device_version
 * @property string $device_width
 * @property string $device_height
 * @property string $app_version_number
 * @property string $device_push_token
 * @property string $device_uuid
 * @property string $device_serial
 * @property bool $notification_mobile
 * @property bool $notification_mobile_newsgroup
 * @property bool $notification_mobile_newsgroup_comment
 * @property bool $notification_mobile_publication
 * @property bool $notification_mobile_publication_comment
 * @property bool $notification_mobile_suggestion
 * @property bool $notification_mobile_suggestion_comment
 * @property bool $notification_mobile_poll
 * @property bool $notification_mobile_alert
 * @property bool $notification_mobile_mayor
 * @property bool $notification_mobile_discussion_message
 *
 * @property \App\Model\Entity\UserCity $user_city
 */
class MandataryDevice extends Entity
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
