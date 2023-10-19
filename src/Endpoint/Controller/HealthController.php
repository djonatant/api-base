<?php

namespace Entersis\Endpoint\Controller;

use Entersis\Endpoint\EndpointBase;
use Entersis\Response;

class HealthController extends EndpointBase
{
    public function get()
    {
        $response = [
            'status' => 'OK',
            'message' => 'API is up and running!',
        ];
        Response::success($response);
    }
}
