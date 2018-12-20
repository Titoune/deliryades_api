<?php

namespace App\Controller\Apibundle\Userbundle;


use App\Utility\Tools;
use Cake\Collection\Collection;
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

    public function getPolls($skip = 0)
    {
        $polls = $this->Polls->find()->contain(['PollProposals']);
        $polls->order(['Polls.created' => 'desc'])->limit(20)->offset($skip);

        $this->api_response_data['polls'] = $polls;
    }

    public function deletePoll($poll_id)
    {
        $this->request->allowMethod('delete');

        $poll = $this->Polls->find()->where(['Polls.id' => $poll_id, 'Polls.user_id' => $this->payloads->user->id])->first();

        if (!$poll) {
            $this->api_response_code = 404;
            $this->api_response_flash = "Sondage introuvable";
        } else {
            if ($this->Polls->delete($poll)) {
                $this->api_response_flash = "Le sondage est maintenant supprimé";
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de la suppression du sondage";
            }
        }
    }

    public function setCreateForm()
    {
        $this->request->allowMethod('post');
        $this->transformRequestData();

        $poll = $this->Polls->newEntity([
            'user_id' => $this->payloads->user->id
        ]);

        $poll = $this->Polls->patchEntity($poll, $this->request->getData(), ['fields' => ['title', 'question', 'description', 'expiration', 'poll_proposals'], 'associated' => ['PollProposals' => ['fields' => ['content']]]]);

        if ($r = $this->Polls->save($poll)) {
            $this->api_response_flash = "Votre sondage a bien été crée";
        } else {
            $this->api_response_code = 400;
            $this->api_response_data['_form']['errors'] = Tools::getErrors($poll->getErrors());
        }
    }

    public function setUpdateForm($poll_id)
    {
        $this->request->allowMethod('patch');
        $this->transformRequestData();

        $poll = $this->Polls->find()->contain(['PollProposals'])->where(['Polls.id' => $poll_id, 'Polls.user_id' => $this->payloads->user->id])->first();
        if ($poll) {

            $original_poll_proposals = $poll->poll_proposals;

            $poll = $this->Polls->patchEntity($poll, $this->request->getData(), ['fields' => ['title', 'question', 'description', 'expiration', 'poll_proposals'], 'associated' => ['PollProposals' => ['fields' => ['content']]]]);

            if (!$poll->getErrors()) {

                if ($r = $this->Polls->save($poll)) {
                    $ids = (new Collection($r->poll_proposals))->extract('id')->toArray();

                    foreach ($original_poll_proposals AS $o) {
                        if (!in_array($o->id, $ids)) {
                            //delete old proposal
                            $this->Polls->PollProposals->delete($o);
                        }
                    }
                    $this->api_response_code = 200;
                    $this->api_response_flash = "Votre sondage a bien été modifié";
                } else {
                    $this->api_response_code = 400;
                    $this->api_response_data['_form']['errors'] = Tools::getErrors($poll->getErrors());
                }
            } else {
                $this->api_response_code = 400;
                $this->api_response_data['_form']['errors'] = Tools::getErrors($poll->getErrors());
            }


        } else {
            $this->api_response_code = 400;
            $this->api_response_flash = "Sondage introuvable";
        }
    }

}
