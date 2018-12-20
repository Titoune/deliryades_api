<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PollProposal Entity
 *
 * @property int $id
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $poll_answer_count
 * @property int $poll_id
 *
 * @property \App\Model\Entity\Poll $poll
 * @property \App\Model\Entity\PollAnswer[] $poll_answers
 */
class PollProposal extends Entity
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
        'content' => true,
        'created' => true,
        'modified' => true,
        'poll_answer_count' => true,
        'poll_id' => true,
        'poll' => true,
        'poll_answers' => true
    ];

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
