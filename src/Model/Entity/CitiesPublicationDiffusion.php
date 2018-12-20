<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CitiesPublicationDiffusion Entity
 *
 * @property int $id
 * @property int $city_id
 * @property int $publication_diffusion_id
 * @property bool $accept
 * @property bool $refuse
 * @property \Cake\I18n\FrozenTime $deleted
 * @property bool $cron_in_progress
 * @property bool $notified
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\PublicationDiffusion $publication_diffusion
 */
class CitiesPublicationDiffusion extends Entity
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
