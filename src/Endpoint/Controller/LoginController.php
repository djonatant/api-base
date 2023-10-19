<?php

namespace Entersis\Endpoint\Controller;

use Entersis\Endpoint\Auth\Authenticator;
use Entersis\Endpoint\EndpointBase;
use Entersis\Enum\General;

class LoginController extends EndpointBase
{

    protected $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function post()
    {
        $postData = json_decode(file_get_contents("php://input"));
        if(is_null($postData)) {
            $this->respondError('Parametros necessários nao informados');
        }
        $email = $postData->email;
        $password = $postData->password;

        if ($this->authenticateUser($email, $password)) {
            $token = self::generateToken($email);
            $this->respondSuccess([
                'user' => [
                    'accessToken' => $token
                ]
            ]);
        } else {
            $this->respondError('Unauthorized', 401);
        }
    }

    public function authenticateUser($email, $password)
    {
        try {
            $query = "SELECT usupassword as password FROM users WHERE usuemail = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->user = $user;
            return ($user && password_verify($password, $user['password']));
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function generateToken($username)
    {
        $secretKey = General::SECRET_JWT;
        $issuedAt = time();
        $expirationTime = $issuedAt + 10800;

        $payload = [
            'username' => $username,
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        ];

        return \Firebase\JWT\JWT::encode($payload, $secretKey, 'HS256');
    }
}