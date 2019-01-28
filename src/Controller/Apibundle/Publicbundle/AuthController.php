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


    public function setUserPasswordLostForm()
    {
        $this->request->allowMethod('post');
        $user = $this->Users->find()->where([
            'OR' => [
                ['Users.cellphone' => $this->request->getData('credential')],
                ['Users.email' => $this->request->getData('credential')]
            ]

        ])->first();
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
        $this->renewPrincipalSession($this->request->getData('credential'), $this->request->getData('password'));
    }

    public function setRegistrationForm()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->newEntity([
            'registered' => 0,
            'token' => Tools::_getRandomHash()
        ]);


        $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => ['sex', 'firstname', 'lastname', 'email', 'password1', 'is_website_terms_of_use_accepted']]);
        $user->password = $user->password1;

        if ($this->request->getData('code')) {
            $this->loadModel('Families');
            $family = $this->Families->find()->where(['Families.code' => $this->request->getData('code')])->first();
            if ($family) {
                $user->family_id = $family->id;
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Code famille invalide";
            }
        }

        if ($this->api_response_code != 400) {
            if ($r = $this->Users->save($user)) :

                $url = $this->shortUrl(WEBSITE_URL . 'auth/email-validation?email=' . $user->email . '&token=' . $user->token);
                $email = new Email('default');
                $email->setEmailFormat('html')
                    ->setTo($user->email)
                    ->setSubject(__("Deliryades - Création de votre compte"))
                    ->setTemplate('user_registration')
                    ->setViewVars(['url' => $url, 'user' => $user, 'password' => $this->request->getData('password1')])
                    ->send();
                $this->api_response_flash = 'Vous allez recevoir un email de confirmation.';
            else:
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($user->getErrors());
            endif;
        }

    }

    public function checkUserRegistration()
    {
        $this->request->allowMethod('post');

        $this->loadModel('Users');
        $user = $this->Users->find()->where(['email' => $this->request->getData('email'), 'token' => $this->request->getData('token'), 'registered' => 0])->first();

        if ($user) {
            $this->Users->query()->update()->set(['token' => Tools::_getRandomHash(), 'registered' => 1])->where(['id' => $user->id])->execute();
            $this->api_response_flash = "Vous pouvez maintenant vous connecter";

        } else {
            $this->api_response_code = 405;
            $this->api_response_flash = "Ce lien n'est plus valable";
        }
    }
}