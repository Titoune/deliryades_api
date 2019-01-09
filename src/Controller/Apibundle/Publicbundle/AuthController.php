<?php

namespace App\Controller\Apibundle\Publicbundle;


use App\Utility\Tools;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Routing\Router;


class AuthController extends InitController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Users');
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function getUserForPasswordRegenerate($email = null, $token = null)
    {
        $this->request->allowMethod('post');
        $user = $this->Users->find()->where(['Users.email' => $this->request->getData('email'), 'Users.token' => $this->request->getData('token')])->first();

        if ($user) {
            $this->api_response_data['user'] = $user;
        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Utilisateur introuvable";
        }
    }


    public function setUserPasswordLostForm()
    {
        $this->request->allowMethod('post');
        $user = $this->Users->find()->where(['Users.cellphone' => $this->request->getData('cellphone')])->first();
        if ($user) {
            $password = rand(1000, 9999);
            $url = $this->shortUrl(WEBSITE_URL);
            $email = new Email('default');
            $email->setEmailFormat('html')
                ->setTo($user->email)
                ->setSubject("Deliryades - Mot de passe provisoire")
                ->setTemplate('new_password')
                ->setViewVars(['password' => $password, 'user' => $user, 'url' => $url]);
            if ($email->send()) {
                $user->password = $password;
                if ($this->Users->save($user)) {
                    $this->api_response_flash = "Vous allez recevoir dans quelques instants un mot de passe provisoire à l'adresse email indiquée sur votre profil.";
                } else {
                    $this->api_response_code = 400;
                    $this->api_response_flash = "Une erreur est survenue lors de la génération d'un mot de passe aléatoire";
                }
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de l'envoi de l'email";
            }
        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = 'Ce numéro ne correspond à aucun compte';
        }
    }


    public function setUserLoginForm()
    {
        $this->request->allowMethod('post');
        $this->renewPrincipalSession($this->request->getData('cellphone'), $this->request->getData('password'));
    }
}