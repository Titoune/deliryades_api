<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $email_notification
 * @property string $password
 * @property string $picture
 * @property \Cake\I18n\FrozenDate $birth
 * @property string $presentation
 * @property string $token
 * @property string $sex
 * @property string $phone
 * @property string $cellphone
 * @property bool $registered
 * @property bool $activated
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logged
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $login_attempt_count
 * @property int $message_unread_count
 * @property bool $no_notification_email
 * @property bool $notification_sms
 * @property bool $newsletter
 * @property int $mayor_count
 * @property int $citizen_count
 * @property int $administrator_count
 * @property int $mandatary_count
 * @property int $mayor_id
 * @property int $user_id
 * @property int $procuration_id
 * @property int $administrator_id
 * @property string $autoconnect_type
 * @property int $autoconnect_usercity_id
 *
 * @property \App\Model\Entity\Mayor[] $mayors
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Administrator[] $administrators
 * @property \App\Model\Entity\AdClick[] $ad_clicks
 * @property \App\Model\Entity\AdDisplay[] $ad_displays
 * @property \App\Model\Entity\AdminBookmark[] $admin_bookmarks
 * @property \App\Model\Entity\AdminExchange[] $admin_exchanges
 * @property \App\Model\Entity\AdminFile[] $admin_files
 * @property \App\Model\Entity\City[] $cities
 * @property \App\Model\Entity\CityReminder[] $city_reminders
 * @property \App\Model\Entity\DiscussionUser[] $discussion_users
 * @property \App\Model\Entity\Log[] $logs
 * @property \App\Model\Entity\Mandatary[] $mandataries
 * @property \App\Model\Entity\NewsgroupComment[] $newsgroup_comments
 * @property \App\Model\Entity\Notification[] $notifications
 * @property \App\Model\Entity\PollAnswer[] $poll_answers
 * @property \App\Model\Entity\PublicationComment[] $publication_comments
 * @property \App\Model\Entity\PublicationLike[] $publication_likes
 * @property \App\Model\Entity\Publication[] $publications
 * @property \App\Model\Entity\Report[] $reports
 * @property \App\Model\Entity\Signaling[] $signalings
 * @property \App\Model\Entity\SmsCampaignGroupUser[] $sms_campaign_group_users
 * @property \App\Model\Entity\SuggestionComment[] $suggestion_comments
 * @property \App\Model\Entity\SuggestionLike[] $suggestion_likes
 * @property \App\Model\Entity\Suggestion[] $suggestions
 * @property \App\Model\Entity\UserArchive[] $user_archives
 * @property \App\Model\Entity\UserCity[] $user_cities
 * @property \App\Model\Entity\UserCv[] $user_cvs
 * @property \App\Model\Entity\UserItemPosition[] $user_item_positions
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
        '*' => true,
        'id' => false,
        'logged' => false,
        'token' => false,
        'created' => false,
        'modified' => false
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

    protected $_virtual = ['picture_sizes', 'fullname', 'age', 'sex_to_text', 'birth_to_date_fr'];


    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'users' . DS . $this->id . DS . $this->picture);
        } else {
            if ($this->mayor_count > 0) {
                if ($this->sex == 'f') {
                    $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_MAYOR_WOMAN);
                } else {
                    $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_MAYOR_MAN);
                }
            } else {
                if ($this->sex == 'f') {
                    $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_CITIZEN_WOMAN);
                } else {
                    $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_CITIZEN_MAN);
                }
            }
        }

        return [
            'xs' => $pic_url . "&width=60&height=60",
            'sm' => $pic_url . "&width=60&height=60",
            'md' => $pic_url . "&width=100&height=100",
            'lg' => $pic_url . "&width=120&height=120",
            'default' => $pic_url,
        ];

    }

    protected function _getBirthToDateFr()
    {
        if ($this->birth) {
            return $this->birth->format('d F Y');
        } else {
            return null;
        }
    }


    protected
    function _getFullname()
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
        return (!empty($val)) ? ucfirst(strip_tags(trim($val))) : null;
    }


    protected function _setFirstname($val)
    {
        return (!empty($val)) ? ucfirst(strip_tags(trim($val))) : null;
    }


    protected function _setEmail($val)
    {
        return (!empty($val)) ? mb_strtolower(strip_tags(trim($val)), 'UTF-8') : null;
    }

    protected function _setEmailNotification($val)
    {
        return (!empty($val)) ? mb_strtolower(strip_tags(trim($val)), 'UTF-8') : null;
    }

    protected function _setPassword($val)
    {
        return !empty($val) ? (new DefaultPasswordHasher)->hash($val) : null;
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
