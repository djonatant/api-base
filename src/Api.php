<?php

namespace Entersis;

class API
{

    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function handleRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $resource = explode('/', rtrim($_GET['q'], '/'));

        switch ($requestMethod) {
            case 'GET':
                $this->handleGET($resource);
                break;
            case 'POST':
                $this->handlePOST($resource);
                break;
            case 'PUT':
                $this->handlePUT($resource);
                break;
            case 'DELETE':
                $this->handleDELETE($resource);
                break;
            default:
                $this->respond(405, 'Method Not Allowed');
                break;
        }
    }

    private function handleGET($resource)
    {
        if (count($resource) == 1 && is_numeric($resource[0])) {
            $id = (int)$resource[0];
            if (isset($this->data[$id])) {
                $this->respond(200, $this->data[$id]);
            } else {
                $this->respond(404, 'Resource Not Found');
            }
        } else {
            $this->respond(400, 'Bad Request');
        }
    }

    private function handlePOST($resource)
    {
        $endpointName = ucfirst($resource[0]) . 'Endpoint';
        $endpointClass = '\\Entersis\\Endpoint\\' . $endpointName;

        if (class_exists($endpointClass)) {
            $endpoint = new $endpointClass($this->db);
            $endpoint->post();
        } else {
            $this->respond(404, 'Resource Not Found');
        }
    }

    private function handlePUT($resource)
    {
        // Implemente a lógica para atualizar um recurso aqui
    }

    private function handleDELETE($resource)
    {
        // Implemente a lógica para excluir um recurso aqui
    }

    private function respond($status, $data)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
