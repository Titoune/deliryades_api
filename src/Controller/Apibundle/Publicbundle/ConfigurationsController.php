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

    public function getWebsiteTermsOfUse()
    {
        $configuration = $this->Configurations->find()->first();
        if ($configuration) {
            $this->api_response_data['website_terms_of_use'] = $configuration->website_terms_of_use;
        } else {
            $this->api_response_code = 404;
        }
    }

    public function getOpenRegistration()
    {
        $configuration = $this->Configurations->find()->first();
        if ($configuration) {
            $this->api_response_data['open_registration'] = $configuration->open_registration;
        } else {
            $this->api_response_code = 404;
        }
    }

}
