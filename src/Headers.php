<?php

namespace Entersis;

class Headers
{

    public static function setCORSHeaders($allowedOrigins = "*", $allowedMethods = "GET, POST, PUT, DELETE, OPTIONS", $allowedHeaders = "Content-Type, Authorization")
    {
        header("Access-Control-Allow-Origin: " . $allowedOrigins);
        header("Access-Control-Allow-Methods: " . $allowedMethods);
        header("Access-Control-Allow-Headers: " . $allowedHeaders);
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit();
        }
    }

}