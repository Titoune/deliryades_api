<?php

namespace App\Controller\Apibundle\Adminbundle;


use Cake\Event\Event;


/**
 * SurveyAnswers Controller
 *
 * @property \App\Model\Table\SurveyAnswersTable $SurveyAnswers
 *
 * @method \App\Model\Entity\SurveyAnswer[] paginate($object = null, array $settings = [])
 */
class PollAnswersController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
}
