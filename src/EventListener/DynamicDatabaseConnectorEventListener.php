<?php
// src/EventListener/DynamicDatabaseConnectorEventListener.php
namespace App\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Model\DbConnectionChanger;

class DynamicDatabaseConnectorEventListener
{
    private $connection;
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Connection $connection, TokenStorageInterface $security)
    {
        $this->connection = $connection;
        $this->em = $em;
        $this->security = $security;
    }

    public function onKernelRequest()
    {
        $connection = $this->connection;

        $security = $this->security;
        if (is_object($security->getToken()) && is_object($user = $security->getToken()->getUser())) {
            $clientId = $user->getClient()->getId();
            $em = $this->em;
//            $client = $em->find('Master:Client', $clientId);
            $client = $em->find('Client:Master\Client', $clientId);
            if (!$connection->isConnected()) {
                $dbConnectionChanger = new DbConnectionChanger($connection);
                $dbConnectionChanger->setConnectionParams(
                    $client->getDbName(),
                    $client->getDbUserName(),
                    $client->getDbUserPassword()
                );
                $dbConnectionChanger->reconnect();
//                $dbParams = $connection->getParams();
//                echo '<pre>';
//                print_r($dbParams);
//                echo '</pre>';
            }

        }
    }
}
