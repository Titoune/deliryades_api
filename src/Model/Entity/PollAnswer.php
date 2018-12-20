<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PollAnswer Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $user_id
 * @property int $poll_proposal_id
 * @property int $poll_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\PollProposal $poll_proposal
 * @property \App\Model\Entity\Poll $poll
 */
class PollAnswer extends Entity
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
        'user_id' => true,
        'poll_proposal_id' => true,
        'poll_id' => true,
        'user' => true,
        'poll_proposal' => true,
        'poll' => true
    ];
}
