<?php

namespace App\Model\Behavior;

use App\Utility\Tools;
use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use ArrayObject;

class LogBehavior extends Behavior
{


    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->createLog($event, $entity, $options);
    }

    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->createLog($event, $entity, $options);
    }


    private function createLog($event, $entity, $options)
    {
        $request = Router::getRequest();
        if (in_array($request->getParam('prefix'), ['apibundle/citizenbundle', 'apibundle/mayorbundle', 'apibundle/administratorbundle'])) {
            $logs = TableRegistry::get('Logs');
            $payloads = Tools::decodeJwt($request->getHeaderLine('Authorization'));
            $log = $logs->newEntity([
                'created' => date('Y-m-d H:i:s'),
                'controller' => $request->getParam('controller'),
                'action' => $request->getParam('action'),
                'foreign_id' => $entity->id,
                'city_id' => $payloads->current_city_id,
                'user_type' => $payloads->user_type,
                'user_id' => $payloads->user->id,
                'ip' => $request->clientIp()]);
            $logs->save($log);
        }
    }
}
