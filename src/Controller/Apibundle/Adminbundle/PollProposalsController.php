<?php

namespace App\Controller\Apibundle\Adminbundle;

use Cake\Event\Event;


/**
 * SurveyProposals Controller
 *
 * @property \App\Model\Table\SurveyProposalsTable $SurveyProposals
 *
 * @method \App\Model\Entity\SurveyProposal[] paginate($object = null, array $settings = [])
 */
class PollProposalsController extends InitController
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
