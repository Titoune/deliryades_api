<?php

namespace App\Shell;

use App\Controller\Apibundle\InitController;
use Cake\Console\Shell;
use Cake\Mailer\Email;

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
            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    // envoi notif birthday
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
            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    // envoi notification phone
                }
            }

            $event->cellphone_notified = 1;
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
            foreach ($users AS $user) {
                foreach ($user->devices AS $device) {
                    // envoi notif sondage
                }
            }

            $poll->cellphone_notified = 1;
            $this->Polls->save($poll);
        }
    }
}
