<?php

namespace App\Controller\Apibundle\Adminbundle;


use Cake\Event\Event;


/**
 * Surveys Controller
 *
 * @property \App\Model\Table\SurveysTable $Surveys
 *
 * @method \App\Model\Entity\Survey[] paginate($object = null, array $settings = [])
 */
class PollsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function getPolls($user_id = null, $skip = 0)
    {
        $polls = $this->Polls->find()->contain(['Cities.Departments', 'PollProposals']);
        $polls->order(['Polls.created' => 'desc'])->limit(40)->offset($skip);

        $this->api_response_data['polls'] = $polls;
    }

}
