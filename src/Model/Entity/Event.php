<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $title
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime|null $start
 * @property \Cake\I18n\FrozenTime|null $end
 * @property float|null $price
 * @property string|null $email
 * @property string|null $cellphone
 * @property string|null $phone
 * @property string|null $street_number
 * @property string|null $route
 * @property string|null $postal_code
 * @property string|null $locality
 * @property string|null $country
 * @property string|null $country_short
 * @property float|null $lat
 * @property float|null $lng
 * @property int|null $event_comment_count
 * @property int|null $event_participation_count
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\EventParticipation[] $event_participations
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
        'created' => true,
        'modified' => true,
        'title' => true,
        'content' => true,
        'start' => true,
        'end' => true,
        'price' => true,
        'email' => true,
        'cellphone' => true,
        'phone' => true,
        'street_number' => true,
        'route' => true,
        'postal_code' => true,
        'locality' => true,
        'country' => true,
        'country_short' => true,
        'lat' => true,
        'lng' => true,
        'event_comment_count' => true,
        'event_participation_count' => true,
        'user_id' => true,
        'user' => true,
        'event_participations' => true
    ];

    protected $_virtual = ['duration'];


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

    protected function _setTitle($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setEmail($val)
    {
        return (!empty($val)) ? mb_strtolower(strip_tags(trim($val)), 'UTF-8') : null;
    }

    protected function _setPhone($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getPhone($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setCellPhone($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getCellphone($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
