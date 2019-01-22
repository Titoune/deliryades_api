<?php

namespace App\Controller\Apibundle\Userbundle;

use App\Utility\Tools;
use Cake\Event\Event;

class UsersController extends InitController
{

    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }


    public function getUsers($skip = 0)
    {
        $users = $this->Users->find();
        $users->order(['Users.firstname' => 'asc'])->limit(100)->offset($skip);
        $this->api_response_data['users'] = $users;
    }

    public function getUser($user_id)
    {
        $user = $this->Users->find()->where(['Users.id' => $user_id])->first();
        if ($user) {
            $this->api_response_data['user'] = $user;
        } else {
            $this->api_response_code = 400;
        }
    }


    public function setUpdateForm()
    {
        $this->request->allowMethod('patch');
        $this->transformRequestData();

        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {

            $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => ['firstname', 'lastname', 'email', 'sex', 'cellphone_prefix', 'cellphone', 'phone_prefix', 'phone', 'birth', 'death', 'presentation', 'branch', 'profession', 'street_number', 'route', 'postal_code', 'locality', 'country', 'lat', 'lng']]);

            if ($r = $this->Users->save($user)) {
                $this->api_response_flash = "Votre profil a bien été modifié";
            } else {
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($user->getErrors());
            }
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Compte introuvable";
        }
    }

    public function setPasswordUpdateForm()
    {
        $this->request->allowMethod('patch');
        $this->transformRequestData();

        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {

            $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => ['password1', 'password2']]);

            if ($r = $this->Users->save($user)) {
                $this->api_response_flash = "Votre mot de passe a bien été modifié";
            } else {
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($user->getErrors());
            }
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Compte introuvable";
        }
    }

    public function setUserField()
    {
        $this->request->allowMethod('patch');
        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => ['firstname', 'lastname', 'email', 'sex', 'cellphone', 'phone', 'birth', 'death', 'presentation', 'branch', 'profession', 'notification_cellphone_anniversary', 'notification_cellphone_event', 'notification_cellphone_poll', 'notification_email_anniversary', 'notification_email_poll', 'notification_email_event']]);
            if ($this->Users->save($user)) {

            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Veuillez vérifier le champ";
            }

        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Utilisateur introuvable";
        }
    }


    public function getUserByName()
    {

        $this->request->allowMethod('post');

        $users = $this->Users->find();

        $terms = $this->request->getData('terms');
        $users->andWhere(function ($exp, $query) use ($terms) {
            $conc = $query->func()->concat([
                'Users.firstname' => 'identifier', ' ',
                'Users.lastname' => 'identifier'
            ]);
            return $exp->like($conc, '%' . $terms . '%');
        });

        $users->order(['Users.lastname' => 'asc'])->limit(20);
        $this->api_response_data['users'] = $users;
    }


    //////////////////////////////

    public function getMe($user_id)
    {
        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {
            $this->api_response_data['user'] = $user;
        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Utilisateur introuvable";
        }
    }


    public function searchUserByEmail()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->find()
            ->where(['Users.email' => trim($this->request->getData('email'))])
            ->first();
        if ($user) {
            $this->api_response_data['user'] = $user;
        } else {
            $this->api_response_code = 404;
        }
    }


    public function uploadUserPicture()
    {
        $this->request->allowMethod('post');
        $user = $this->Users->find()->where(['Users.id' => $this->payloads->user->id])->first();
        if ($user) {

            if (in_array($this->request->getData('file.type'), ['image/jpg', 'image/jpeg', 'image/png'])) {

                if ($this->request->getData('file.size') < 500000000000000000) {
                    $path_info = pathinfo($this->request->getData('file.name'));
                    $picture_final_folder = MEDIA_USER_PATH . $this->payloads->user->id;
                    $picture_name = Tools::_getRandomFilename(15) . '.' . strtolower($path_info['extension']);
                    $picture_final_path = $picture_final_folder . DS . $picture_name;

                    if ($this->createFolderIfNotExist($picture_final_folder)) {
                        if (move_uploaded_file($this->request->getData('file.tmp_name'), $picture_final_path)) {
                            $filename = pathinfo($user->picture, PATHINFO_FILENAME);
                            $user->picture = $picture_name;
                            if ($this->Users->save($user)) {
                                if ($filename) {
                                    array_map('unlink', glob(MEDIA_USER_PATH . $this->payloads->user->id . DS . $filename . "*"));
                                }
                            } else {
                                $this->api_response_code = 400;
                                $this->api_response_flash = "Une erreur est survenue lors de la sauvegarde de la photo";
                            }

                        } else {
                            $this->api_response_code = 400;
                            $this->api_response_flash = "Une erreur est survenue lors du déplacement de la photo";
                        }
                    } else {
                        $this->api_response_code = 400;
                        $this->api_response_flash = "Une erreur est survenue lors de l'upload, veuillez réessayer";
                    }
                } else {
                    $this->api_response_code = 400;
                    $this->api_response_flash = "La taille maximum de photo autorisé est de 20 mo";
                }

            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Veuillez vérifier le type de fichier";
            }

        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Utilisateur introuvable, veuillez réessayer";
        }
    }

}
