<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Firebase {

    protected $firebase;

    public function __construct() {
        $config = array(
            'keyFilePath' => APPPATH . 'config/firebase_credentials.json',
            'databaseUri' => 'https://nali-ab2d7-default-rtdb.firebaseio.com/',
        );

        $serviceAccount = ServiceAccount::fromJsonFile($config['keyFilePath']);
        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri($config['databaseUri'])
            ->create();
    }

    public function getDatabase() {
        return $this->firebase->getDatabase();
    }

}
