<?php

namespace App\Controller\Apibundle\Userbundle;

use App\Utility\Tools;
use Cake\Event\Event;

class ChatCommentsController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }


    public function getChatComments($skip = 0)
    {
        $chat_comments = $this->ChatComments->find()->contain(['Users']);
        $chat_comments->order(['ChatComments.created' => 'asc'])->limit(100)->offset($skip);
        $this->api_response_data['chat_comments'] = $chat_comments;
    }

    public function setCreateForm()
    {
        $this->request->allowMethod('post');

        $chat_comment = $this->ChatComments->newEntity([
            'user_id' => $this->payloads->user->id
        ]);

        $chat_comment = $this->ChatComments->patchEntity($chat_comment, $this->request->getData(), ['fields' => ['content']]);

        if ($r = $this->ChatComments->save($chat_comment)) {

        } else {
            $this->api_response_code = 400;
            $this->api_response_data['_form']['errors'] = Tools::getErrors($chat_comment->getErrors());
        }
    }


    public function deleteChatComment($chat_comment_id)
    {
        $this->request->allowMethod('delete');

        $chat_comment = $this->ChatComments->find()->where(['ChatComments.id' => $chat_comment_id, 'ChatComments.user_id' => $this->payloads->user->id])->first();

        if (!$chat_comment) {
            $this->api_response_code = 404;
            $this->api_response_flash = "Message introuvable";
        } else {
            if ($this->ChatComments->delete($chat_comment)) {
                $this->api_response_flash = "Le message est supprimé";
            } else {
                $this->api_response_code = 400;
                $this->api_response_flash = "Une erreur est survenue lors de la suppression du message";
            }
        }
    }



}
