<?php

namespace App\Controller\Apibundle\Userbundle;


use Cake\Event\Event;

class PollAnswersController extends InitController
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
