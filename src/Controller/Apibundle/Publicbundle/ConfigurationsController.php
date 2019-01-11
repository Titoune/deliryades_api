<?php

namespace App\Controller\Apibundle\Publicbundle;

use App\Utility\Tools;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;

class ConfigurationsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function setBugReportCreateForm()
    {
        $this->request->allowMethod('post');

        $email = new Email('default');
        $email->setEmailFormat('html')
            ->setTo(EMAIL_FROM_EMAIL)
            ->setSubject("BUG sur le site");
        if ($email->send(json_encode($this->request->getData()))) {
            $this->api_response_flash = "Message envoyé, merci de votre participation";
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Une erreur est survenue lors de l'envoi du message, veuillez réeesayer";
        }

    }

}
