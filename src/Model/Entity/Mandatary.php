<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Mandatary Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property int $mayor_id
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logged
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $permission_publication_comment_module
 * @property int $permission_discussion_module
 * @property int $permission_alert_module
 * @property int $permission_newsgroup_module
 * @property int $permission_newsgroup_comment_module
 * @property int $permission_publication_module
 * @property int $permission_event_module
 * @property int $permission_city_module
 * @property int $permission_poll_module
 * @property int $permission_sms_campaign_module
 * @property int $permission_mayor_module
 * @property int $permission_signaling_module
 * @property int $permission_city_negociation_module
 * @property int $permission_suggestion_module
 * @property int $permission_statistic_module
 * @property int $permission_user_city_module
 * @property int $permission_suggestion_comment_module
 * @property int $permission_ad_module
 * @property int $permission_sms_campaign_group_module
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Mayor $mayor
 */
class Mandatary extends Entity
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
        '*' => true,
        'id' => false,
        'logged' => false,
        'token' => false,
        'created' => false,
        'modified' => false
    ];
}
