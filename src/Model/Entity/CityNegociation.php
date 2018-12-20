<?php

namespace App\Model\Entity;

use App\Utility\Options;
use Cake\ORM\Entity;

/**
 * CityNegociation Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property int $city_id
 * @property string $name
 * @property int $type
 * @property string $description
 * @property string $information
 * @property string $incharge
 * @property string $filename
 * @property string $phone
 * @property string $email
 * @property float $price_initial
 * @property float $price_final
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 *
 * @property \App\Model\Entity\City $city
 */
class CityNegociation extends Entity
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

    protected $_virtual = ['status_to_text', 'type_to_text', 'pdf_picture_sizes', 'pdf_url'];

    protected function _getStatus_to_text()
    {
        return Options::getNegociationStatus($this->status);

    }

    protected function _getType_to_text()
    {
        return Options::getCityNegociationTypes($this->type);

    }


    protected function _getPdf_picture_sizes()
    {
        if ($this->filename) {
            $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'devis' . DS . $this->id . DS . pathinfo($this->filename, PATHINFO_FILENAME) . '.jpg');
        } else {
            $pic_url = IMAGE_RESIZE_URL . urlencode('img' . DS . 'introuvable.jpg');
        }

        return [
            'xs' => $pic_url . "&width=300&height=150",
            'sm' => $pic_url . "&width=600&height=300",
            'md' => $pic_url . "&width=800&height=400",
            'lg' => $pic_url . "&width=1200&height=600",
            'default' => $pic_url,
        ];
    }

    protected function _getPdf_url()
    {
        if ($this->filename) {
            return WEBSITE_URL . 'medias' . DS . 'devis' . DS . $this->id . DS . $this->filename;
        } else {
            return null;
        }
    }


    protected function _setName($val)
    {
        return !empty($val) ? trim($val) : null;
    }

    protected function _setDescription($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setInformation($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }

    protected function _setIncharge($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
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


    protected function _setEmail($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }


}
