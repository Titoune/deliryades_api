<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * Suggestion Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $suggestion_category_id
 * @property int $user_id
 * @property int $city_id
 * @property string $title
 * @property string $content
 * @property int $status
 * @property bool $engagement
 * @property int $engagement_percentage
 * @property int $engagement_number
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $closed
 * @property string $mayor_comment
 * @property string $mayor_response
 * @property int $for_count
 * @property int $against_count
 * @property int $vote_count
 * @property int $comment_count
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\SuggestionCategory $suggestion_category
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\SuggestionComment[] $suggestion_comments
 */
class Suggestion extends Entity
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
    protected $_virtual = ['status_to_text'];

    protected function _getStatus_to_text()
    {
        return Options::getSuggestionStatusOptions($this->status);
    }

    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setMayor_response($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setMayor_comment($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
