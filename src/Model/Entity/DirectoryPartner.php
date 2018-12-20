<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DirectoryPartner Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $website
 * @property string $description
 *
 * @property \App\Model\Entity\DirectoryPartnerCategoriesDirectoryPartner[] $directory_partner_categories_directory_partners
 * @property \App\Model\Entity\DirectoryPartnerCategory[] $directory_partner_categories
 * @property \App\Model\Entity\Department[] $departments
 */
class DirectoryPartner extends Entity
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
    protected $_virtual = ['picture_url'];



    protected function _getPicture_url()
    {

        $pic_url = IMAGE_RESIZE_URL . urlencode('medias' . DS . 'partners' . DS . $this->id . DS . $this->picture);


        return [
            'xs' => $pic_url . "&width=60&height=60",
            'sm' => $pic_url . "&width=100&height=100",
            'md' => $pic_url . "&width=120&height=120",
            'lg' => $pic_url,
            'default' => $pic_url,
        ];

    }
}
