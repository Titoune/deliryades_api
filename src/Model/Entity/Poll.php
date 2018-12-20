<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Poll Entity
 *
 * @property int $id
 * @property string|null $question
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime|null $expiration
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $poll_proposal_count
 * @property int|null $poll_answer_count
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\PollAnswer[] $poll_answers
 * @property \App\Model\Entity\PollProposal[] $poll_proposals
 */
class Poll extends Entity
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
        'question' => true,
        'content' => true,
        'expiration' => true,
        'created' => true,
        'modified' => true,
        'poll_proposal_count' => true,
        'poll_answer_count' => true,
        'user_id' => true,
        'user' => true,
        'poll_answers' => true,
        'poll_proposals' => true
    ];

    protected function _setQuestion($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
