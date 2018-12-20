<?php

namespace App\Utility;

use Google\Cloud\Firestore\FirestoreClient;

class Firestore
{
    private $client;

    public function __construct()
    {
        $this->client = new FirestoreClient([
            'keyFile' => FIRESTORE_KEY
        ]);
    }

    public function insert($collection, $id, $data)
    {
        try {
            $collection = $this->client->collection($collection);
            $collection->document($id)->set($data);
        } catch (\Exception $e) {
            unset($e);
        }
    }

    public function delete($collection, $id)
    {
        try {
            $collection = $this->client->collection($collection);
            $collection->document($id)->delete();
        } catch (\Exception $e) {
            unset($e);
        }
    }
}
