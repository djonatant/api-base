<?php

namespace Entersis;

class Response
{
    public static function success($data = null)
    {
        $response = [
            'success' => true,
            'status' => 200,
            'info' => $data
        ];
        self::sendResponse($response);
    }

    public static function error($message, $status = 400)
    {
        $response = [
            'success' => false,
            'status' => $status,
            'info' => [
                'message' => $message
            ]
        ];
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    private static function sendResponse($data)
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
