<?php

namespace App\Controller\Apibundle\Userbundle;

use Cake\Event\Event;

class DevicesController extends InitController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }


    public function setUpdateForm($uuid)
    {
        $this->request->allowMethod('patch');
        $device = $this->Devices->find()->where(['Devices.uuid' => $uuid, 'Devices.user_id' => $this->payloads->user->id])->first();
        if (!$device) {
            $device = $this->Devices->newEntity([
                'user_id' => $this->payloads->user->id
            ]);

        }
        $device = $this->Devices->patchEntity($device, $this->request->getData(), ['fields' => ['device_push_token', 'api', 'uuid', 'manufacturer', 'model', 'version', 'platform']]);

        if ($r = $this->Devices->save($device)) {
        } else {
            $this->api_response_code = 400;
        }
    }

    public function deleteDevice($uuid)
    {
        $this->request->allowMethod('delete');

        $device = $this->Devices->find()->where(['Devices.uuid' => $uuid, 'Devices.user_id' => $this->payloads->user->id])->first();

        if (!$device) {
            $this->api_response_code = 404;
        } else {
            if ($this->Devices->delete($device)) {

            } else {
                $this->api_response_code = 400;
            }
        }
    }


}
