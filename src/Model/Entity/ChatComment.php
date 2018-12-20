<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChatComment Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $content
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 */
class ChatComment extends Entity
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
        'content' => true,
        'user_id' => true,
        'user' => true
    ];

    protected function _setContent($val)
    {
        return !empty($val) ? strip_tags(trim($val)) : null;
    }
}
