<?php

namespace App\Controller\Apibundle\Adminbundle;


use Cake\Event\Event;

/**
 * Cities Controller
 *
 * @property \App\Model\Table\CitiesTable $Cities
 *
 * @method \App\Model\Entity\City[] paginate($object = null, array $settings = [])
 */
class UserItemPositionsController extends InitController
{

    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }


}
