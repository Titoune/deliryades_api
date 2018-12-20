<?php

namespace App\Controller\Apibundle\Userbundle;

use App\Utility\Tools;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;

class EventsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function getMonthEvents($month, $year)
    {
        $month = sprintf("%02d", ($month + 1));

        $events = $this->Events->find()
            ->where([
                'OR' => [
                    [
                        'MONTH(Events.start)' => $month,
                        'YEAR(Events.start)' => $year
                    ],
                    [
                        'MONTH(Events.end)' => $month,
                        'YEAR(Events.end)' => $year
                    ]
                ]
            ])->order(['Events.start' => 'asc']);


        $this->api_response_data['month_events'] = $events;

    }

    public function getDayEvents($date = null)
    {

        $dateExplode = explode('-', $date);
        $events = $this->Events->find()
            ->where([
                'OR' => [
                    [
                        'YEAR(Events.start)' => $dateExplode[0],
                        'MONTH(Events.start)' => $dateExplode[1],
                        'DAY(Events.start) >=' => $dateExplode[2],
                        'YEAR(Events.end)' => $dateExplode[0],
                        'MONTH(Events.end)' => $dateExplode[1],
                        'DAY(Events.end) <=' => $dateExplode[2],
                        'Events.end != ' => $date . ' 00:00:00'
                    ],
                    [
                        'YEAR(Events.start)' => $dateExplode[0],
                        'MONTH(Events.start)' => $dateExplode[1],
                        'DAY(Events.start) <=' => $dateExplode[2],
                        'YEAR(Events.end)' => $dateExplode[0],
                        'MONTH(Events.end)' => $dateExplode[1],
                        'DAY(Events.end) >=' => $dateExplode[2],
                        'Events.end != ' => $date . ' 00:00:00'
                    ]
                ],
            ])
            ->order('Events.start ASC');
        $this->api_response_data['day_events'] = $events;
    }

    public function deleteEvent($event_id)
    {
        $this->request->allowMethod('delete');

        $event = $this->Events->find()->where(['Events.id' => $event_id, 'Events.user_id' => $this->payloads->user->id])->first();

        if (!$event) {
            $this->api_response_code = 404;
            $this->api_response_flash = "Evènement introuvable";
        } else {
            if ($this->Events->delete($event)) {
                $this->api_response_flash = "L'évènement a bien été supprimé";
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de la suppression de l'évènement";
            }
        }
    }


    public function getEvent($event_id)
    {
        $event = $this->Events->find()->where(['Events.id' => $event_id, 'Events.user_id' => $this->payloads->user->id])->first();
        if ($event) {
            $this->api_response_data['event'] = $event;
        } else {
            $this->api_response_code = 404;
            $this->api_response_flash = "Evènement introuvable";
        }
    }


    public function setCreateForm()
    {
        $this->request->allowMethod('post');
        $this->transformRequestData();

        $event = $this->Events->newEntity([
            'user_id' => $this->payloads->user->id
        ]);


        $event = $this->Events->patchEntity($event, $this->request->getData(), ['fields' => ['title', 'content', 'start', 'end']]);
        if ($event->end instanceof FrozenTime) {
            if ($event->end->format('His') == '000000') {
                $event->end = $event->end->modify('-1 sec');
            }
        }

        if ($r = $this->Events->save($event)) {

            $this->api_response_code = 200;
            $this->api_response_flash = "Votre évènement a bien été crée";
        } else {
            $this->api_response_code = 400;
            $this->api_response_data['_form']['errors'] = Tools::getErrors($event->getErrors());
        }
    }

    public function setUpdateForm($event_id)
    {
        $this->request->allowMethod('patch');
        $this->transformRequestData();

        $event = $this->Events->find()->where(['Events.id' => $event_id, 'Events.user_id' => $this->payloads->user->id])->first();
        if ($event) {

            $event = $this->Events->patchEntity($event, $this->request->getData(), ['fields' => ['title', 'content', 'start', 'end']]);
            if ($event->end instanceof FrozenTime) {
                if ($event->end->format('His') == '000000') {
                    $event->end = $event->end->modify('-1 sec');
                }
            }
            if ($r = $this->Events->save($event)) {

                $this->api_response_code = 200;
                $this->api_response_flash = "Votre évènement a bien été modifié";
            } else {
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($event->getErrors());
            }
        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Evènement introuvable";
        }
    }

}
