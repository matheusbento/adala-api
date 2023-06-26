<?php

namespace App;

use Aws\Handler\GuzzleV6\GuzzleHandler as BaseAwsGuzzleHandler;
use GuzzleHttp\Client;

class GuzzleHandler extends BaseAwsGuzzleHandler
{
    public function __construct()
    {
        // dd('dasd');
        parent::__construct(new Client(
            ['verify' => false]
        ));
    }
}
