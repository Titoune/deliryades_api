<?php

namespace App\Controller\Apibundle\Userbundle;

use App\Utility\Tools;
use Cake\Event\Event;

class DiscussionCommentsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function getDiscussionComments($skip = 0)
    {
        $discussion_comments = $this->DiscussionComments->find();
        $discussion_comments->order(['DiscussionComments.created' => 'asc'])->limit(100)->offset($skip);
        $this->api_response_data['discussion_comments'] = $discussion_comments;
    }

    public function getDiscussionCommentsByUser($user_id, $skip = 0)
    {
        $discussion_comments = $this->DiscussionComments->find()->where([
            'OR' => [
                ['DiscussionComments.sender_id' => $user_id],
                ['DiscussionComments.receiver_id' => $user_id]
            ]
        ]);
        $discussion_comments->order(['DiscussionComments.created' => 'asc'])->limit(100)->offset($skip);
        $this->api_response_data['discussion_comments'] = $discussion_comments;
    }

    public function setCreateForm()
    {
        $this->request->allowMethod('post');

        $discussion_comment = $this->DiscussionComments->newEntity([
            'user_id' => $this->payloads->user->id
        ]);

        $discussion_comment = $this->DiscussionComments->patchEntity($discussion_comment, $this->request->getData(), ['fields' => ['content']]);

        if ($r = $this->DiscussionComments->save($discussion_comment)) {

        } else {
            $this->api_response_code = 400;
            $this->api_response_data['_form']['errors'] = Tools::getErrors($discussion_comment->getErrors());
        }
    }


    public function deleteDiscussionComment($discussion_comment_id)
    {
        $this->request->allowMethod('delete');

        $discussion_comment = $this->DiscussionComments->find()->where(['DiscussionComments.id' => $discussion_comment_id, 'DiscussionComments.user_id' => $this->payloads->user->id])->first();

        if (!$discussion_comment) {
            $this->api_response_code = 404;
            $this->api_response_flash = "Message introuvable";
        } else {
            if ($this->DiscussionComments->delete($discussion_comment)) {
                $this->api_response_flash = "Le message est supprimé";
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de la suppression du message";
            }
        }
    }

}
