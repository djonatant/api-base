<?php

namespace Entersis\Endpoint\Controller;

use Entersis\Endpoint\EndpointBase;
use Entersis\Enum\General;
use Firebase\JWT\Key;

class SignupController extends EndpointBase
{
    public function post()
    {
        try {
            $postData = json_decode(file_get_contents("php://input"));
            if(is_null($postData)) {
                $this->respondError('Parametros necessários nao informados');
                return;
            }
            $this->validateEmail($postData->mail);
            $this->beginTransaction();
            $hashedPassword = password_hash($postData->password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (usunome, usuemail, usupassword) VALUES (:nome, :email, :password)";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':nome', $postData->name);
            $stmt->bindParam(':email', $postData->mail);
            $stmt->bindParam(':password', $hashedPassword);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $token = LoginController::generateToken($postData->mail);

                try {
                    $jwtKey = new Key(General::SECRET_JWT, 'HS256');
                    $options = new \stdClass();
                    $options->algorithm = 'HS256';
                    $decodedToken = \Firebase\JWT\JWT::decode($token, $jwtKey, $options);
                    $sql = "INSERT INTO user_info (usucodigo) VALUES ((SELECT usucodigo FROM users WHERE usuemail = :username))";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(':username', $decodedToken->username);
                    $stmt->execute();
                    $this->commitTransaction();
                } catch (\Exception $e) {
                    $this->respondError('Failed to decode JWT token.');
                    return;
                }

                $this->respondSuccess([
                    'user' => [
                        'accessToken' => $token
                    ]
                ]);
            } else {
                $this->rollbackTransaction();
                $this->respondError('Failed to create user.');
            }
        } catch (\PDOException $e) {
            $this->rollbackTransaction();
            $this->respondError('Internal Server Error', 500);
        }
    }

    private function validateEmail($email)
    {
        try {
            $query = "SELECT usuemail as email FROM users WHERE usuemail = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if($user) {
                $this->respondError('Usuário já cadastrado com este e-mail.');
            }
        } catch (\PDOException $e) {
            return false;
        }
    }

}
