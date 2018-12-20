<?php

namespace App\Controller\Publicbundle;

use Cake\Event\Event;

class PublicationsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event)
    {
        return parent::beforeFilter($event);
    }

    public function redirectPublicPublication($uniqid)
    {
        //obsolete > 2.0.8
        return $this->redirect(WEBSITE_URL . 'publications/share/' . $uniqid);
    }
}

