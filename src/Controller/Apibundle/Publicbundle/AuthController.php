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


    public function checkJwt()
    {
        $this->api_response_code = 200;
    }


    public function registrationValidation($email, $token)
    {
        $user = $this->Users->find()->contain(['UserCities'])->where(['email' => urldecode($email), 'token' => $token, 'registered' => 0])->first();

        if ($user) {
            $this->Users->query()->update()->set(['token' => Tools::_getRandomHash(), 'registered' => 1, 'login_attempt_count' => 0])->where(['id' => $user->id])->execute();
            //Si login ok on supprime les LoginAttempts
            $this->loadModel('LoginAttempts');
            $this->LoginAttempts->deleteAll(['login' => $user->email]);
            $this->api_response_flash = "Votre compte est maintenant activé, vous pouvez vous connecter avec vos identifiants";
        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = 'Ce lien n\'est plus valable';
        }
    }

    public function getUserPasswordLostForm()
    {
        $this->api_response_data['_form']['validations'] = Tools::getValidations($this->Users->getValidator('default')->getIterator(), 'create');
    }

    public function getUserPasswordRegenerateForm()
    {
        $this->api_response_data['_form']['validations'] = Tools::getValidations($this->Users->getValidator('default')->getIterator(), 'create');
    }

    public function getUserForPasswordRegenerate($email = null, $token = null)
    {

        if (version_compare($this->request->getQuery('api'), '3.0.0') >= 0) {
            $this->request->allowMethod('post');
            $user = $this->Users->find()->where(['Users.email' => $this->request->getData('email'), 'Users.token' => $this->request->getData('token')])->first();
        } else {
            $user = $this->Users->find()->where(['Users.email' => $email, 'Users.token' => $token])->first();
        }

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


    public function getUserLoginForm()
    {
        $this->api_response_data['_form']['validations'] = Tools::getValidations($this->Users->getValidator('default')->getIterator(), 'create');
    }


    public function setUserLoginForm()
    {
        $this->request->allowMethod('post');
        $this->renewPrincipalSession($this->request->getData('email'), $this->request->getData('password'));
    }


    public function checkUserRegistration($email = null, $token = null)
    {
        if (version_compare($this->request->getQuery('api'), '3.0.0') >= 0) {
            $this->request->allowMethod('post');
            $user = $this->Users->find()->where(['email' => $this->request->getData('email'), 'token' => $this->request->getData('token'), 'registered' => 0])->first();

        } else {
            $user = $this->Users->find()->where(['email' => urldecode($email), 'token' => $token, 'registered' => 0])->first();

        }
        if ($user) {
            $this->Users->query()->update()->set(['token' => Tools::_getRandomHash(), 'registered' => 1, 'login_attempt_count' => 0])->where(['id' => $user->id])->execute();
            $this->loadModel('LoginAttempts');
            $this->LoginAttempts->deleteAll(['login' => $user->email]);
            $this->api_response_flash = "Vous pouvez maintenant vous connecter";

        } else {
            $this->api_response_code = 405;
            $this->api_response_flash = "Ce lien n'est plus valable";
        }
    }


    public function checkAppRequirements()
    {
        //obsolete when > 2.0.7
    }


    public function getUserRegistrationForm()
    {
        $this->api_response_data['_form']['fields'] = null;
        $this->api_response_data['_form']['validations'] = Tools::getValidations($this->Users->getValidator('default')->getIterator(), 'create');
    }

    public function setUserRegistrationForm()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->newEntity([
            'registered' => 0,
            'newsletter' => 1,
        ]);

        $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'registration', 'fields' => ['firstname', 'lastname', 'email', 'presentation', 'password1', 'is_website_terms_of_use_accepted']]);
        $user->password = $user->password1;
        $user->email_notification = $user->email;

        if ($r = $this->Users->save($user)) :

            $url = $this->shortUrl(Router::url(['prefix' => 'publicbundle', 'controller' => 'Auth', 'action' => 'emailValidation', '?' => ['email' => $user->email, 'token' => $user->token]], true));
            $email = new Email('default');
            $email->setEmailFormat('html')
                ->setAttachments([
                    'manuel-utilisation-citoyen.pdf' => [
                        'file' => 'manuel-utilisation-citoyen.pdf',
                        'mimetype' => 'application/pdf',
                    ]
                ])
                ->setTo($user->email)
                ->setSubject(__("MairesetCitoyens.fr - Création de votre compte"))
                ->setTemplate('from_user_to_user_new_registration')
                ->setViewVars(['url' => $url, 'user' => $user, 'password' => $this->request->getData('password1')])
                ->send();
            $this->api_response_flash = 'Vous allez recevoir un email de confirmation.';
        else:
            $this->api_response_code = 400;
            $this->api_response_data['_form']['errors'] = Tools::getErrors($user->getErrors());
        endif;
    }
}