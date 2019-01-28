<?php

namespace App\Controller\Apibundle\Userbundle;


use App\Utility\Tools;
use Cake\Event\Event;
use Cake\Mailer\Email;

class AuthController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function setCodeForm()
    {
        $this->request->allowMethod('post');
        $this->transformRequestData();

        $this->loadModel('Users');
        $this->loadModel('Families');

        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {
            $family = $this->Families->find()->where(['code' => $this->request->getData('code')])->first();
            if ($family) {
                $user->family_id = $family->id;
                if ($this->Users->save($user)) {
                    $this->api_response_flash = "Vous êtes maintenant associé à la famille";

                } else {
                    $this->api_response_code = 400;
                    $this->api_response_flash = "Une erreur c'est produite, veuillez réessayer";
                }
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Code famille introuvable";
            }
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Une erreur c'est produite, veuillez réessayer";
        }
    }

    public function setFamilyCreateForm()
    {
        $this->request->allowMethod('post');
        $this->transformRequestData();

        $this->loadModel('Users');
        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {
            $this->loadModel('Families');
            $family = $this->Families->newEntity([
                'code' => Tools::_getRandomHash()
            ]);


            $family = $this->Families->patchEntity($family, $this->request->getData(), ['fields' => ['name']]);

            if ($r = $this->Families->save($family)) {
                $user->family_id = $r->id;
                $this->Users->save($user);
                $this->api_response_code = 200;
                $this->api_response_flash = "Votre famille a bien été créée";
            } else {
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($family->getErrors());
            }
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Une erreur c'est produite, veuillez réessayer";
        }


    }

    public function sendRegistrationCode()
    {
        $this->request->allowMethod('post');
        $this->transformRequestData();

        $this->loadModel('Families');
        $family = $this->Families->find()->where(['id' => $this->payloads->user->family_id])->first();
        if ($family) {
            $url = $this->shortUrl(WEBSITE_URL . 'auth/registration?email=' . $this->request->getData('email') . '&code=' . $family->code);
            $email = new Email('default');
            $email->setEmailFormat('html')
                ->setTo($this->request->getData('email'))
                ->setSubject(__("Deliryades - Invitation"))
                ->setTemplate('user_invitation')
                ->setViewVars(['url' => $url, 'code' => $family->code])
                ->send();
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Une erreur c'est produite, veuillez réessayer";
        }
    }
}
