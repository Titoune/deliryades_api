<?php

namespace App\Controller\Apibundle\Adminbundle;

use Cake\Event\Event;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[] paginate($object = null, array $settings = [])
 */
class EventsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function getEvents($user_id = null, $skip = 0)
    {
        $events = $this->Events->find()->contain(['Cities.Departments']);
        $events->order(['Events.start' => 'desc'])->limit(40)->offset($skip);

        $this->api_response_data['events'] = $events;
    }

}
