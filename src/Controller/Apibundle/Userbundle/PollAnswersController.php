<?php

namespace App\Controller\Apibundle\Userbundle;


use Cake\Event\Event;

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

    public function setCreateForm()
    {
        $this->request->allowMethod('post');


        $poll_answer = $this->PollAnswers->find()->where(['PollAnswers.poll_id' => $this->request->getData('poll_id'), 'PollAnswers.user_id' => $this->payloads->user->id])->first();
        if (!$poll_answer) {
            $poll_proposal = $this->PollAnswers->PollProposals->find()->contain(['Polls'])->where(['PollProposals.id' => $this->request->getData('proposal_id'), 'PollProposals.poll_id' => $this->request->getData('poll_id'), 'Polls.expiration >=' => date('Y-m-d H:i:s')])->first();
            if ($poll_proposal) {

                $poll_answer = $this->PollAnswers->newEntity([
                    'poll_id' => $poll_proposal->poll_id,
                    'poll_proposal_id' => $poll_proposal->id,
                    'user_id' => $this->payloads->user->id
                ]);

                if ($r = $this->PollAnswers->save($poll_answer)) {
                    $this->api_response_flash = 'Votre vote a bien été pris en compte.';
                    $this->api_response_data['poll_answer'] = $poll_answer;
                } else {
                    $this->api_response_flash = 'Une erreur est survenue lors du vote.';
                }

            } else {
                $this->api_response_code = 403;
                $this->api_response_flash = 'Le sondage à expiré';
            }
        } else {
            $this->api_response_code = 403;
            $this->api_response_flash = 'Vous avez déja voté pour ce sondage';
        }
    }
}
