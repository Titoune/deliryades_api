<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Page Entity
 *
 * @property int $id
 * @property string $uniqid
 * @property string $name
 * @property string $prefix
 * @property string $controller
 * @property string $action
 * @property string $title1
 * @property string $title2
 * @property string $title3
 * @property string $title4
 * @property string $title5
 * @property string $title6
 * @property string $title7
 * @property string $title8
 * @property string $title9
 * @property string $title10
 * @property string $content1
 * @property string $content2
 * @property string $content3
 * @property string $content4
 * @property string $content5
 * @property string $content6
 * @property string $content7
 * @property string $content8
 * @property string $content9
 * @property string $content10
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Page extends Entity
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
}
