<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Picture Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $filename
 * @property int $position
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $model
 * @property int $foreign_id
 *
 */
class Picture extends Entity
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

    protected $_virtual = ['picture_sizes'];


    protected function _getPicture_sizes()
    {
        if ($this->filename) {
            if ($this->initial_publication_id) {
                $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'publications' . DS . 'shared' . DS . $this->initial_publication_id . DS . $this->filename);
            } else {
                $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . strtolower($this->model) . DS . $this->foreign_id . DS . $this->filename);
            }

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

    protected function _setFilename($val)
    {
        return !empty($val) ? $val : null;
    }


}
