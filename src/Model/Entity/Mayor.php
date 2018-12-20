<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * Mayor Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logged
 * @property \Cake\I18n\FrozenTime $deleted
 * @property string $since
 * @property string $slogan
 * @property string $slug
 * @property int $notification_count
 * @property int $notification_user_city
 * @property int $notification_publication_comment
 * @property int $notification_suggestion
 * @property int $notification_suggestion_comment
 * @property int $notification_newsgroup_comment
 * @property int $notification_discussion_message
 * @property int $notification_user_city_frequency
 * @property int $notification_publication_comment_frequency
 * @property int $notification_suggestion_frequency
 * @property int $notification_suggestion_comment_frequency
 * @property int $notification_newsgroup_comment_frequency
 * @property int $notification_discussion_message_frequency
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Mandatary[] $mandataries
 */
class Mayor extends Entity
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

    protected function _setSlogan($val)
    {
        return (!empty($val)) ? ucfirst(strip_tags(trim($val))) : null;
    }

    protected function _setSlug($val)
    {
        return (!empty($val)) ? Text::slug(mb_strtolower(strip_tags(trim($val)), 'UTF-8')) : null;
    }

    protected function _setSince($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setPresentation($val)
    {
        return (!empty($val)) ? ucfirst(strip_tags(trim($val))) : null;
    }


}
