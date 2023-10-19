<?php

namespace Entersis\Endpoint\Auth;

use Entersis\Endpoint\EndpointBase;
use Entersis\Enum\General;
use Entersis\Response;
use Firebase\JWT\Key;

class Authenticator extends EndpointBase
{

    private $secretKey = General::SECRET_JWT; // Substitua pela sua chave secreta

    public static function verifyToken()
    {
        $token = self::getTokenFromHeader();

        if (!$token) {
            Response::error('Unauthorized', 401);
        }

        if (self::validateToken($token)) {
            // Token válido
            return true;
        } else {
            Response::error('Unauthorized', 401);
        }
    }

    public static function getDecodedToken()
    {
        $jwtKey = new Key(General::SECRET_JWT, 'HS256');
        $options = new \stdClass();
        $options->algorithm = 'HS256';
        $token = self::getTokenFromHeader();
        return \Firebase\JWT\JWT::decode($token, $jwtKey, $options);
    }

    public static function getEncodedToken()
    {
        return self::getTokenFromHeader();
    }

    private static function getTokenFromHeader()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader);
            return $token;
        }
        return null;
    }

    private static function validateToken($token)
    {
        $jwtKey = new Key(General::SECRET_JWT, 'HS256');
        $options = new \stdClass();
        $options->algorithm = 'HS256';

        try {
            $decoded = \Firebase\JWT\JWT::decode($token, $jwtKey, $options);
            return true; // Token válido
        } catch (\Exception $e) {
            return false; // Token inválido ou expirado
        }
    }

}