<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;
use JCrowe\BadWordFilter\BadWordFilter;

/**
 * SuggestionComment Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $user_id
 * @property int $suggestion_id
 * @property int $vote
 * @property string $content
 * @property bool $anonymous
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $up_count
 * @property int $down_count
 * @property int $report_count
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Suggestion $suggestion
 * @property \App\Model\Entity\SuggestionLike[] $suggestion_likes
 */
class SuggestionComment extends Entity
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

    protected $_virtual = ['vote_to_text'];

    protected function _getVote_to_text()
    {
        return Options::getVoteOptionArray($this->vote);

    }


    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getContent($content)
    {
        if (!empty($content)) {

            $myOptions = array("strictness" => "permissive", "also_check" => Options::getBadwordsArray());
            $filter = new BadWordFilter($myOptions);
            $content = $filter->clean($content);

        }

        return $content;
    }
}
