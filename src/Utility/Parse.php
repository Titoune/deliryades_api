<?php

namespace App\Utility;

use Parse\ParseClient;
use Parse\ParseObject;

class Parse
{

    public function __construct()
    {
        ParseClient::initialize('sdrysdtfdqsqssdffdjhssdfhdsdfhdhdfhhdf', null, 'sdfhsdfertezrttsdfrgsdfdggjdfgjdfjshs');
        ParseClient::setServerURL('http://localhost:1337','parse');
    }

    public function insert($collection, $data)
    {
        try {
            $object = ParseObject::create($collection);

            foreach($data AS $k => $v)
            {
                $object->set($k, $v);
            }

            $object->save();

        } catch (\Exception $e) {
            debug($e);
            die;
            unset($e);
        }
    }

    public function delete($collection, $id)
    {
        try {
//            $collection = $this->client->collection($collection);
//            $collection->document($id)->delete();
        } catch (\Exception $e) {
            unset($e);
        }
    }
}
