<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EventParticipation Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $event_participation_type
 * @property int $user_id
 * @property int $event_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Event $event
 */
class EventParticipation extends Entity
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
        'created' => true,
        'modified' => true,
        'event_participation_type' => true,
        'user_id' => true,
        'event_id' => true,
        'user' => true,
        'event' => true
    ];
}
