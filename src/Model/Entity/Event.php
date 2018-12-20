<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $title
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime|null $start
 * @property \Cake\I18n\FrozenTime|null $end
 * @property float|null $price
 * @property string|null $email
 * @property string|null $cellphone
 * @property string|null $phone
 * @property string|null $street_number
 * @property string|null $route
 * @property string|null $postal_code
 * @property string|null $locality
 * @property string|null $country
 * @property string|null $country_short
 * @property float|null $lat
 * @property float|null $lng
 * @property int|null $event_comment_count
 * @property int|null $event_participation_count
 * @property int $user_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\EventParticipation[] $event_participations
 */
class Event extends Entity
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
        'title' => true,
        'content' => true,
        'start' => true,
        'end' => true,
        'price' => true,
        'email' => true,
        'cellphone' => true,
        'phone' => true,
        'street_number' => true,
        'route' => true,
        'postal_code' => true,
        'locality' => true,
        'country' => true,
        'country_short' => true,
        'lat' => true,
        'lng' => true,
        'event_comment_count' => true,
        'event_participation_count' => true,
        'user_id' => true,
        'user' => true,
        'event_participations' => true
    ];
}
