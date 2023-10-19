<?php

namespace Entersis;

use PDO;

class Database
{

    private $connection;

    public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->connection = new \PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
            die();
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
