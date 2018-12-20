<?php

namespace App\Controller\Apibundle\Userbundle;

use App\Controller\Apibundle\InitController as InitialController;
use App\Utility\Tools;
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
        return $this->checkBundleAccess('user');

    }

}
