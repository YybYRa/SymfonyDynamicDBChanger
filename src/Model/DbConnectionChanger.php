<?php
// src/Model/DbConnectionChanger.php

namespace App\Model;

use Doctrine\DBAL\Connection;

class DbConnectionChanger
{
    private $connection;
    private $dBParams;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function setConnectionParams(string $dbname, string $user, string $password)
    {
        $oldConnection = $this->connection;
        $dbParams = $oldConnection->getParams();
        $dbParams["user"] = $user;
        $dbParams["dbname"] = $dbname;
        $dbParams["password"] = $password;
        $dbParams["url"] = 'mysql://' . $user . ':' . $password . '@' . $dbParams["host"] . '/' . $dbname;
        $this->dBParams = $dbParams;
        return $dbParams;
    }

    public function reconnect()
    {
        $connection = $this->connection;
        try {
            $connection->__construct(
                $this->dBParams,
                $connection->getDriver(),
                $connection->getConfiguration(),
                $connection->getEventManager()
            );
            //            $connection->connect();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}