<?php

namespace App\Controller\Apibundle\Publicbundle;

use App\Controller\Apibundle\InitController as InitialController;
use Cake\Event\Event;


class InitController extends InitialController
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
