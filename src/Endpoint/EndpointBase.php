<?php

namespace Entersis\Endpoint;

use Entersis\Database;
use Entersis\Response;

class EndpointBase
{
    /**
     * @var Database
     */
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    protected function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    protected function commitTransaction()
    {
        $this->db->commit();
    }

    protected function rollbackTransaction()
    {
        $this->db->rollBack();
    }

    protected function respondSuccess(array $message)
    {
        Response::success($message);
    }

    protected function respondError($message, $status = 400)
    {
        Response::error($message, $status);
    }
}
