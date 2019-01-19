<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $email
 * @property string|null $password
 * @property \Cake\I18n\FrozenTime|null $logged
 * @property string|null $picture
 * @property string|null $sex
 * @property string|null $token
 * @property string|null $street_number
 * @property string|null $route
 * @property string|null $postal_code
 * @property string|null $locality
 * @property string|null $country
 * @property string|null $country_short
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $cellphone
 * @property string|null $phone
 * @property \Cake\I18n\FrozenDate|null $birth
 * @property \Cake\I18n\FrozenDate|null $death
 * @property string|null $presentation
 * @property string|null $branch
 * @property string|null $profession
 * @property int|null $admin
 * @property int|null $notification_cellphone_anniversary
 * @property int|null $notification_cellphone_event
 * @property int|null $notification_cellphone_poll
 * @property int|null $notification_email_anniversary
 * @property int|null $notification_email_poll
 * @property int|null $notification_email_event
 * @property string|null $device_push_token
 *
 * @property \App\Model\Entity\ChatComment[] $chat_comments
 * @property \App\Model\Entity\EventComment[] $event_comments
 * @property \App\Model\Entity\EventParticipation[] $event_participations
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\PollAnswer[] $poll_answers
 * @property \App\Model\Entity\Poll[] $polls
 */
class User extends Entity
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
        'firstname' => true,
        'lastname' => true,
        'email' => true,
        'password' => true,
        'logged' => true,
        'picture' => true,
        'sex' => true,
        'token' => true,
        'street_number' => true,
        'route' => true,
        'postal_code' => true,
        'locality' => true,
        'country' => true,
        'country_short' => true,
        'lat' => true,
        'lng' => true,
        'cellphone' => true,
        'phone' => true,
        'birth' => true,
        'death' => true,
        'presentation' => true,
        'branch' => true,
        'profession' => true,
        'admin' => true,
        'notification_cellphone_anniversary' => true,
        'notification_cellphone_event' => true,
        'notification_cellphone_poll' => true,
        'notification_email_anniversary' => true,
        'notification_email_poll' => true,
        'notification_email_event' => true,
        'device_push_token' => true,
        'chat_comments' => true,
        'event_comments' => true,
        'event_participations' => true,
        'events' => true,
        'poll_answers' => true,
        'polls' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
        'token'
    ];

    protected $_virtual = ['fullname', 'age', 'sex_to_text', 'picture_sizes'];

    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode( 'users' . DS . $this->id . DS . $this->picture);
        } else {

            if ($this->sex == 'f') {
                $pic_url = IMAGE_RESIZE_URL . urlencode('womman.jpg');
            } else {
                $pic_url = IMAGE_RESIZE_URL . urlencode('man.jpg');
            }
        }

        return [
            'xs' => $pic_url . "&width=60&height=60",
            'sm' => $pic_url . "&width=120&height=120",
            'md' => $pic_url . "&width=150&height=150",
            'lg' => $pic_url . "&width=200&height=200",

            'default' => $pic_url,
        ];

    }

    protected function _getFullname()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
        } else {
            return null;
        }
    }

    protected function _getSexToText()
    {
        if ($this->sex) {
            if ($this->sex == 'm') {
                return 'Homme';
            } elseif ($this->sex == 'f') {
                return 'Femme';
            } elseif ($this->sex == 'i') {
                return 'Indéfini';
            }
            return null;
        } else {
            return null;
        }
    }

    protected function _getAge()
    {
        if (!$this->birth) {
            return null;
        }

        return date_diff(date_create($this->birth->format('Y-m-d H:i:s')), date_create('today'))->y;
    }

    protected function _getBirthday()
    {
        if (!$this->birth) {
            return null;
        }

        $today_day = date('m-d');
        $birth_day = $this->birth->format('m-d');
        $year = date('Y');

        if ($birth_day < $today_day) :
            $year++;
        endif;

        return new FrozenDate($year . '-' . $birth_day . ' 00:00:00');
    }

    protected function _setLastname($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }


    protected function _setFirstname($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }


    protected function _setEmail($val)
    {
        return (!empty($val)) ? mb_strtolower(strip_tags(trim($val)), 'UTF-8') : null;
    }


    protected function _setPassword($val)
    {
        return !empty($val) ? (new DefaultPasswordHasher())->hash($val) : null;
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


    protected function _setPicture($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }
}
