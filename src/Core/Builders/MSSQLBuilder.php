<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 07:57
 */


namespace QueryMapper\Core\Builders;

use QueryMapper\Core\Builders\Base\PDOBuilder;
use QueryMapper\Exceptions\BuilderException;

class MSSQLBuilder extends PDOBuilder
{
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_sqlsrv')) {
                throw new BuilderException("pdo_sqlsrv extension is required.");
            }
            if (!isset($_ENV['MSSQL_DSN']) || !isset($_ENV['MSSQL_USER']) || !isset($_ENV['MSSQL_PASSWORD'])) {
                throw new BuilderException("MSSQL connection parameters are missing. Check the environment variables have loaded correctly.");
            }
            $this->setPDO($this->createPDO($_ENV['MSSQL_DSN'], $_ENV['MSSQL_USER'], $_ENV['MSSQL_PASSWORD']));
        }
    }
}