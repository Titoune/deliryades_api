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
        $user = $this->Users->find()->where(['Users.email' => $this->request->getData('email')])->first();
        if ($user) :
            $url = $this->shortUrl(Router::url(['prefix' => 'publicbundle', 'controller' => 'Auth', 'action' => 'generatePassword', '?' => ['email' => $user->email, 'token' => $user->token]], true));
            $email = new Email('default');
            $email
                ->setEmailFormat('html')
                ->setTo($user->email)
                ->setSubject(__('MairesetCitoyens.fr - Demande de modification de votre mot de passe'))
                ->setTemplate('from_user_to_user_new_password')
                ->setViewVars(['url' => $url, 'user' => $user]);
            if ($email->send()) {
                $this->api_response_flash = "Vous allez recevoir dans quelques instants un lien de réinitialisation de votre mot de passe à l'adresse email indiquée.";
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de l'envoi de l'email";
            }


        else:
            $this->api_response_code = 404;
            $this->api_response_flash = 'Cette email ne correspond à aucun compte';
        endif;

    }


    public function setUserPasswordRegenerateForm()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->find()->where(['Users.email' => $this->request->getData('email'), 'Users.token' => $this->request->getData('token')])->first();
        if ($user) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'default', 'fields' => ['password1']]);
            if (!$user->getErrors()) {
                $user->token = Tools::_getRandomHash();
                $user->password = $user->password1;
                $user->login_attempt_count = 0;
                $this->loadModel('LoginAttempts');
                $this->LoginAttempts->deleteAll(['login' => $user->email]);
                if ($this->Users->save($user)) {


                    $email = new Email('default');
                    $email->setEmailFormat('html')
                        ->setTo($user->email)
                        ->setSubject(__('MairesetCitoyens.fr - Modification de votre mot de passe'))
                        ->setTemplate('from_user_to_user_new_password_confirmation')
                        ->setViewVars(['user' => $user])
                        ->send();

                    $this->api_response_flash = "Votre mot de passe est modifié";
                } else {
                    $this->api_response_code = 400;
                    $this->api_response_flash = "Veuillez vérifier le formulaire";
                }
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Veuillez vérifier le formulaire";
            }

        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Le token n'est pas valable";
        }

    }


    public function setUserLoginForm()
    {
        $this->request->allowMethod('post');
        $this->renewPrincipalSession($this->request->getData('cellphone'), $this->request->getData('password'));
    }
}