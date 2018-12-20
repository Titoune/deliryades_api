<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CitySupposedMayor Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $lastname
 * @property string $firstname
 * @property string $email_pro
 * @property string $email_perso
 * @property string $phone_pro
 * @property string $phone_perso
 * @property string $picture
 * @property string $insee
 * @property string $sex
 * @property \Cake\I18n\FrozenDate $birth
 * @property int $report_count
 * @property bool $no_email_notification
 */
class CitySupposedMayor extends Entity
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


    protected $_virtual = ['picture_sizes', 'fullname'];

    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'supposedmayors' . DS . $this->id . DS . $this->picture);
        } else {

            if ($this->sex == 'f') {
                $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_MAYOR_WOMAN);
            } else {
                $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_MAYOR_MAN);
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


    protected function _getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }


    protected function _setLastname($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setFirstname($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setEmail_pro($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setEmail_perso($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setPhone_pro($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getPhone_pro($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setPhone_perso($val)
    {
        $val = str_replace(array(' ', '.', '-', '/'), '', $val);
        if (substr($val, 0, 2) == '00') {
            $val = '+' . substr($val, 2);
        } elseif (substr($val, 0, 1) == '0') {
            $val = '+33' . substr($val, 1);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _getPhone_perso($val)
    {
        if (substr($val, 0, 3) == '+33') {
            $val = '0' . substr($val, 3);
        }
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setInsee($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
