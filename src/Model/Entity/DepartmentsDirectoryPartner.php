<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DepartmentsDirectoryPartner Entity
 *
 * @property int $id
 * @property int $department_id
 * @property int $directory_partner_id
 *
 * @property \App\Model\Entity\DirectoryPartner $directory_partner
 * @property \App\Model\Entity\Department $department
 */
class DepartmentsDirectoryPartner extends Entity
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
