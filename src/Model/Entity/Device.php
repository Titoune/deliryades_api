<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity
 *
 * @property int $id
 * @property string|null $device_push_token
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $api
 * @property string|null $uuid
 * @property string|null $manufacturer
 * @property string|null $model
 * @property string|null $version
 */
class Device extends Entity
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
        'device_push_token' => true,
        'created' => true,
        'modified' => true,
        'api' => true,
        'uuid' => true,
        'manufacturer' => true,
        'model' => true,
        'version' => true
    ];
}
