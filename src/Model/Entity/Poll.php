<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Poll Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property string $title
 * @property string $question
 * @property string $description
 * @property \Cake\I18n\FrozenTime $expiration
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $answer_count
 * @property int $proposal_count
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\City $city
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
        '*' => true
    ];

    protected $_virtual = ['expiration_to_date_fr', 'expiration_to_time_fr'];


    protected function _getExpirationToDateFr()
    {
        if ($this->expiration) {
            return $this->expiration->i18nFormat('EEEE dd MMMM yyyy');
        } else {
            return null;
        }
    }

    protected function _getExpirationToTimeFr()
    {
        if ($this->expiration) {
            return $this->expiration->format('H:i');
        } else {
            return null;
        }
    }


    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setQuestion($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setDescription($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
