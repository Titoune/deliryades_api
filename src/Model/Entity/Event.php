<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property string $title
 * @property string $content
 * @property int $type
 * @property \Cake\I18n\FrozenTime $start
 * @property \Cake\I18n\FrozenTime $end
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property bool $activated
 * @property bool $cron_in_progress
 * @property bool $notified
 *
 * @property \App\Model\Entity\City $city
 */
class Event extends Entity
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

    protected $_virtual = ['start_to_date_fr', 'start_to_time_fr', 'end_to_date_fr', 'end_to_time_fr', 'duration'];


    protected function _getDuration()
    {
        if ($this->end && $this->start) {
            $duration = null;
            $diff = date_diff($this->end, $this->start);
            if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 23 && $diff->i == 59 && $diff->s == 59) {
                $duration = "Toute la journée";
            } else {
                if ($diff->y > 0)
                    $duration .= $diff->y . " an ";
                if ($diff->m > 0)
                    $duration .= $diff->m . " mois ";
                if ($diff->d > 0) {
                    if ($diff->d == 1) {
                        $duration .= $diff->d . " jour ";
                    } else {
                        $duration .= $diff->d . " jours ";
                    }
                }
                if ($diff->h > 0) {
                    if ($diff->h == 1) {
                        $duration .= $diff->h . " heure ";
                    } else {
                        $duration .= $diff->h . " heures ";
                    }
                }
                if ($diff->i > 0) {
                    if ($diff->i == 1) {
                        $duration .= $diff->i . " minutes ";
                    } else {
                        $duration .= $diff->i . " minutes ";
                    }
                }
            }
            if ($this->end->format('H:i:s') == '00:00:00') {
                $this->end = $this->end->modify('-1 second');
            }
            return $duration;
        } else {
            return null;
        }

    }


    protected function _getStartToDateFr()
    {
        if ($this->start) {
            return $this->start->i18nFormat('EEEE dd MMMM yyyy');
        } else {
            return null;
        }
    }

    protected function _getStartToTimeFr()
    {
        if ($this->start) {
            return $this->start->format('H:i');
        } else {
            return null;
        }
    }

    protected function _getEndToDateFr()
    {
        if ($this->end) {
            return $this->end->i18nFormat('EEEE dd MMMM yyyy');
        } else {
            return null;
        }
    }

    protected function _getEndToTimeFr()
    {
        if ($this->end) {
            return $this->end->format('H:i');
        } else {
            return null;
        }
    }


    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
