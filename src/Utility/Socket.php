<?php

namespace App\Utility;

use Cake\ORM\TableRegistry;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use ElephantIO\Engine\SocketIO\Version1X;

class Socket
{
    private $jwt;

    public function __construct()
    {
        $socket_jwt = [
            'user_type' => 'cron',
            'user_id' => 1,
            'user_fullname' => 'Tache Cron',
            'current_user_id' => 1,
            'platform' => 'web'
        ];
        $this->jwt = Tools::encodeJwt($socket_jwt);
    }


    public function emit($namespace, $event, $data)
    {
        try {
            $client = new Client(new Version2X(SOCKET_URL . '?token=' . $this->jwt, [
                'context' => ['ssl' => ['verify_peer_name' => false, 'verify_peer' => false]],
                'query' => [
                    'token' => $this->jwt
                ],
                'headers' => [
                    'Authorization: ' . $this->jwt
                ]
            ]));

            $client->initialize();
            $client->of($namespace)->emit($event, $data);
            //$client->emit($event, $data);

            $client->close();
        } catch (\Exception $e) {
            unset($e);
        }
    }

}