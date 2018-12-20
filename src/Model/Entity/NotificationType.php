<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NotificationType Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string $model
 * @property string $prefix
 * @property string $controller
 * @property string $action
 * @property string $email_subject
 * @property string $email_message
 * @property string $notification_title
 * @property string $notification_message
 * @property string $type
 *
 * @property \App\Model\Entity\Notification[] $notifications
 */
class NotificationType extends Entity
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
