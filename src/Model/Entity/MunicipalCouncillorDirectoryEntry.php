<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class MunicipalCouncillorDirectoryEntry extends Entity
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
        'id' => false
    ];

    protected $_virtual = ['picture_sizes', 'sex_to_text'];

    protected function _getPhone($val)
    {
        return (!empty($val)) ? str_replace('+33', '0', $val) : null;
    }

    protected function _getPhone2($val)
    {
        return (!empty($val)) ? str_replace('+33', '0', $val) : null;
    }


    protected function _getPicture_sizes()
    {
        if ($this->picture) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'cities' . DS . $this->city_id . DS . $this->picture);
        } else {
          if ($this->sex == 'f') {
              $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_CITIZEN_WOMAN);
          } else {
              $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . IMAGE_DEFAULT_CITIZEN_MAN);
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

    protected function _setEmail2($val)
    {
        return (!empty($val)) ? mb_strtolower(strip_tags(trim($val)), 'UTF-8') : null;
    }


    protected function _setPhone($val)
    {
        if (!empty($val)) {
            $val = strip_tags(trim($val));
            $first = mb_substr($val, 0, 1);
            $second = mb_substr($val, 1, 1);

            if ($first == 0 && (in_array($second, [1, 2, 3, 4, 5, 6, 7, 8, 9]))) {
                $val = substr($val, 1);
                $val = '+33' . $val;
            } elseif ($first == 0 && $second == 0) {
                $val = substr($val, 2);
                $val = '+' . $val;
            }

            return $val;
        }

        return null;
    }

    protected function _setPhone2($val)
    {
        if (!empty($val)) {
            $val = strip_tags(trim($val));
            $first = mb_substr($val, 0, 1);
            $second = mb_substr($val, 1, 1);

            if ($first == 0 && (in_array($second, [1, 2, 3, 4, 5, 6, 7, 8, 9]))) {
                $val = substr($val, 1);
                $val = '+33' . $val;
            } elseif ($first == 0 && $second == 0) {
                $val = substr($val, 2);
                $val = '+' . $val;
            }

            return $val;
        }

        return null;
    }


    protected function _setPicture($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }

    protected function _setProfession($val)
    {
        return (!empty($val)) ? trim($val) : null;
    }


}
