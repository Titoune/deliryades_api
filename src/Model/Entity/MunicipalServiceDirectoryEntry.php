<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class MunicipalServiceDirectoryEntry extends Entity
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


    protected function _getPhone($val)
    {
        return (!empty($val)) ? str_replace('+33', '0', $val) : null;
    }

    protected function _getPhone2($val)
    {
        return (!empty($val)) ? str_replace('+33', '0', $val) : null;
    }


    protected function _setName($val)
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


    protected function _setDescription($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setOpening_hours($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setWebsite($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setStreet_number($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setRoute($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setPostal_code($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }

    protected function _setLocality($val)
    {
        return (!empty($val)) ? strip_tags(trim($val)) : null;
    }
}
