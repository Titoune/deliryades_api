<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Discussion Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\DiscussionMessage[] $discussion_messages
 * @property \App\Model\Entity\DiscussionUser[] $discussion_users
 */
class Discussion extends Entity
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
