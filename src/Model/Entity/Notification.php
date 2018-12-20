<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notification Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property int $notification_type_id
 * @property int $city_id
 * @property string $sender
 * @property string $title
 * @property bool $sent
 * @property bool $readed
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $published
 * @property int $foreign_id
 * @property int $anchor_id
 * @property bool $cron_in_progress
 * @property bool $sent_firebase
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\NotificationType $notification_type
 * @property \App\Model\Entity\City $city
 */
class Notification extends Entity
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

    protected $_virtual = ['icon'];

    protected function _geticon()
    {
        if ($this->notification_type_id == 1)
            return "icon-messagerie";
        if ($this->notification_type_id == 2)
            return "icon-messagerie";
        if ($this->notification_type_id == 3)
            return "icon-news";
        if ($this->notification_type_id == 4)
            return "icon-news";
        if ($this->notification_type_id == 5)
            return "icon-discussions";
        if ($this->notification_type_id == 6)
            return "icon-discussions";
        if ($this->notification_type_id == 7)
            return "people";
        if ($this->notification_type_id == 9)
            return "icon-news";
        if ($this->notification_type_id == 10)
            return "icon-sondage";
        if ($this->notification_type_id == 11)
            return "icon-discussions";
        if ($this->notification_type_id == 12)
            return "icon-alert";
        if ($this->notification_type_id == 13)
            return "icon-suggestion";
        if ($this->notification_type_id == 14)
            return "icon-suggestion";
        if ($this->notification_type_id == 15)
            return "icon-suggestion";
        if ($this->notification_type_id == 16)
            return "icon-suggestion";
        if ($this->notification_type_id == 17)
            return "icon-suggestion";
        if ($this->notification_type_id == 18)
            return "icon-suggestion";
        if ($this->notification_type_id == 19)
            return "icon-signalements";
        if ($this->notification_type_id == 20)
            return "icon-news";
        if ($this->notification_type_id == 21)
            return "icon-share";
        if ($this->notification_type_id == 22)
            return "icon-share";
        if ($this->notification_type_id == 23)
            return "icon-petites-annonces";
        if ($this->notification_type_id == 24)
            return "icon-petites-annonces";
    }
}
