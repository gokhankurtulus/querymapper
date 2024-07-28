<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:56
 */


namespace QueryMapper\Core\Connections;

use QueryMapper\Core\Connection;
use QueryMapper\Exceptions\ConnectionException;

abstract class PDOConnection extends Connection
{
    protected ?\PDO $pdo = null;

    abstract public function initialize(): void;

    public function terminate(): void
    {
        $this->setPDO(null);
    }

    public function __construct()
    {
        if (!extension_loaded('pdo')) {
            throw new ConnectionException("PDO extension is required.");
        }

        parent::__construct();
    }

    public function __destruct()
    {
        $this->terminate();
    }

    protected function getPDO(): ?\PDO
    {
        return $this->pdo;
    }

    protected function setPDO(?\PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    protected function createPDO(string $dsn, string $user = "", string $password = ""): \PDO
    {
        return new \PDO($dsn, $user, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }
}