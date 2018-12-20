<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DiscussionComment Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime|null $read_sender
 * @property \Cake\I18n\FrozenTime|null $read_receiver
 * @property int $sender_id
 * @property int $receiver_id
 *
 * @property \App\Model\Entity\User $user
 */
class DiscussionComment extends Entity
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
        'content' => true,
        'read_sender' => true,
        'read_receiver' => true,
        'sender_id' => true,
        'receiver_id' => true,
        'user' => true
    ];
}
