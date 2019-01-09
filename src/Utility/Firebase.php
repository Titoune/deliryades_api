<?php

namespace App\Utility;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Topic;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class Firebase
{
    private $client;

    public function __construct()
    {
        $server_key = FIREBASE_KEY;
        $this->client = new Client();
        $this->client->setApiKey($server_key);
        $this->client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
    }


    public function subscribe($topic, $token)
    {
        $response = $this->client->addTopicSubscription($topic, [$token]);
        return $response;
    }

    public function unsubscribe($topic, $token)
    {
        $response = $this->client->removeTopicSubscription($topic, [$token]);
        return $response;
    }


    public function sendNotification($devices, $title, $body, $datas)
    {
        $message = new Message();
        $message
            ->setPriority('high')
            ->setNotification(new Notification($title, $body))
            ->setData($datas);

        foreach ($devices AS $device) {
            $message->addRecipient(new Device($device));
        }
        $response = $this->client->send($message);

        return $response->getStatusCode();
    }
}
