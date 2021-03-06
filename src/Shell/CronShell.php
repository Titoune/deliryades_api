<?php

namespace App\Shell;

use App\Controller\Apibundle\InitController;
use Cake\Console\Shell;
use Cake\Mailer\Email;
use App\Utility\Firebase;

class CronShell extends Shell
{
    public function initialize()
    {
        parent::initialize();
    }


    public function sendBirthdaysEmailNotifications()
    {
        $this->loadModel('Users');
        $birthdays = $this->Users->find()
            ->where([
                '(LPAD(DAY(Users.birth), 2, "0")) = ' => date('d'), '(LPAD(MONTH(Users.birth), 2, "0")) = ' => date('m'),
                'Users.death IS ' => null
            ]);
        if ($birthdays->isEmpty()) {
            die;
        }

        $users = $this->Users->find()->where(['Users.notification_email_anniversary' => 1, 'Users.email IS NOT NULL']);
        if ($users->isEmpty()) {
            die;
        }

        $subject = '';
        $url = (new InitController())->shortUrl(WEBSITE_URL);

        foreach ($birthdays AS $k => $birthday) {
            $subject .= ($k == 0) ? $birthday->fullname : ', ' . $birthday->fullname;
        }

        if ($birthdays->count() > 1) {
            $subject .= " fêtent leurs anniversaires aujourd'hui";
        } else {
            $subject .= " fête son anniversaire aujourd'hui";

        }

        foreach ($users AS $u) {
            $email = new Email('default');
            $email->setEmailFormat('html')
                ->setTo($u->email)
                ->setSubject($subject)
                ->setTemplate('notification_birthdays')
                ->setViewVars(['url' => $url, 'user' => $u, 'birthdays' => $birthdays])
                ->send();
        }
    }

    public function sendEventEmailNotifications()
    {
        $this->loadModel('Events');
        $events = $this->Events->find()->contain(['Users'])->where(['Events.email_notified' => 0]);
        if ($events->isEmpty()) {
            die;
        }

        $users = $this->Events->Users->find()->where(['Users.notification_email_event' => 1, 'Users.email IS NOT NULL']);
        if ($users->isEmpty()) {
            die;
        }

        $url = (new InitController())->shortUrl(WEBSITE_URL);

        foreach ($events AS $event) {
            foreach ($users AS $u) {
                $email = new Email('default');
                $email->setEmailFormat('html')
                    ->setTo($u->email)
                    ->setSubject(__("{0} a créé un évènement intitulé \"{1}\"", [$event->user->fullname, $event->title]))
                    ->setTemplate('notification_event')
                    ->setViewVars(['url' => $url, 'user' => $u, 'event' => $event])
                    ->send();
            }

            $event->email_notified = 1;
            $this->Events->save($event);
        }
    }

    public function sendPollEmailNotifications()
    {
        $this->loadModel('Polls');
        $polls = $this->Polls->find()->contain(['Users'])->where(['Polls.email_notified' => 0]);
        if ($polls->isEmpty()) {
            die;
        }

        $users = $this->Polls->Users->find()->where(['Users.notification_email_poll' => 1, 'Users.email IS NOT NULL']);
        if ($users->isEmpty()) {
            die;
        }

        $url = (new InitController())->shortUrl(WEBSITE_URL);

        foreach ($polls AS $poll) {
            foreach ($users AS $u) {
                $email = new Email('default');
                $email->setEmailFormat('html')
                    ->setTo($u->email)
                    ->setSubject(__("{0} a créé un sondage intitulé \"{1}\"", [$poll->user->fullname, $poll->question]))
                    ->setTemplate('notification_poll')
                    ->setViewVars(['url' => $url, 'user' => $u, 'poll' => $poll])
                    ->send();
            }

            $poll->email_notified = 1;
            $this->Polls->save($poll);
        }
    }


    public function sendBirthdaysCellphoneNotifications()
    {
        $this->loadModel('Users');
        $birthdays = $this->Users->find()
            ->where([
                '(LPAD(DAY(Users.birth), 2, "0")) = ' => date('d'), '(LPAD(MONTH(Users.birth), 2, "0")) = ' => date('m'),
                'Users.death IS ' => null
            ]);
        if ($birthdays->isEmpty()) {
            die;
        }

        $users = $this->Users->find()->contain(['Devices'])->where(['notification_cellphone_anniversary' => 1, 'cellphone IS NOT ' => null]);
        if ($users->isEmpty()) {
            die;
        }


        foreach ($birthdays AS $k => $birthday) {
            $receivers = [];
            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    if ($device->device_push_token) {
                        $receivers[] = $device->device_push_token;
                    }
                }
            }

            if (!empty($receivers)) {
                $title = 'Hello';
                $body = $birthday->user->fullname . " fête son anniversaire aujourd'hui!";
                $firebase = new Firebase();
                $redirect_page = 'PublicLoginPage';
                $redirect_params = null;
                $response = $firebase->sendNotification($receivers, $title, $body, ['redirect_page' => $redirect_page, 'redirect_params' => $redirect_params, 'title' => $title, 'content' => $body]);
                if ($response != 200) {

                }
            }

        }
    }

    public function sendEventCellphoneNotifications()
    {
        $this->loadModel('Events');
        $events = $this->Events->find()->contain(['Users'])->where(['Events.cellphone_notified' => 0]);
        if ($events->isEmpty()) {
            die;
        }

        $users = $this->Events->Users->find()->contain(['Devices'])->where(['Users.notification_cellphone_event' => 1, 'Users.cellphone IS NOT NULL']);
        if ($users->isEmpty()) {
            die;
        }

        foreach ($events AS $event) {
            $event->cellphone_notified = 1;
            $receivers = [];
            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    if ($device->device_push_token) {
                        $receivers[] = $device->device_push_token;
                    }
                }
            }

            if (!empty($receivers)) {
                $title = 'Hello';
                $body = $event->user->fullname . " a créé un évènement";
                $firebase = new Firebase();
                $redirect_page = 'PublicLoginPage';
                $redirect_params = null;
                $response = $firebase->sendNotification($receivers, $title, $body, ['redirect_page' => $redirect_page, 'redirect_params' => $redirect_params, 'title' => $title, 'content' => $body]);
                if ($response != 200) {
                    $event->cellphone_notified = 0;
                }
            }
            $this->Events->save($event);
        }
    }

    public function sendPollCellphoneNotifications()
    {
        $this->loadModel('Polls');
        $polls = $this->Polls->find()->contain(['Users'])->where(['Polls.cellphone_notified' => 0]);
        if ($polls->isEmpty()) {
            die;
        }

        $users = $this->Polls->Users->find()->contain(['Devices'])->where(['Users.notification_cellphone_poll' => 1, 'Users.cellphone IS NOT NULL']);
        if ($users->isEmpty()) {
            die;
        }

        foreach ($polls AS $poll) {
            $poll->cellphone_notified = 1;
            $receivers = [];

            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    if ($device->device_push_token) {
                        $receivers[] = $device->device_push_token;
                    }
                }
            }

            if (!empty($receivers)) {
                $title = 'Hello';
                $body = $poll->user->fullname . " a créé un sondage";
                $firebase = new Firebase();
                $redirect_page = 'PublicLoginPage';
                $redirect_params = null;
                $response = $firebase->sendNotification($receivers, $title, $body, ['redirect_page' => $redirect_page, 'redirect_params' => $redirect_params, 'title' => $title, 'content' => $body]);
                if ($response != 200) {
                    $poll->cellphone_notified = 0;
                }
            }

            $this->Polls->save($poll);
        }
    }

    public function tmp()
    {
        $this->loadModel('Users');
        $users = $this->Users->find();
        foreach ($users AS $user) {
            if ($user->sex == 0) {
                $user->sex = 'm';
            } else {
                $user->sex = 'f';
            }

            $user->picture = null;
            $user->cellphone_prefix = '+33';
            $user->phone_prefix = '+33';
            $user->password = '5985';
            if ($user->cellphone) {
                if (substr($user->cellphone, 0, 2) == 33) {
                    $user->cellphone = '0' . substr($user->cellphone, 2);
                }
            }


            if ($user->phone) {
                if (substr($user->phone, 0, 2) == 33) {
                    $user->phone = '0' . substr($user->phone, 2);
                }
            }
            $this->Users->save($user);

        }
    }
}
