<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 11:06
 */


namespace QueryMapper\Core\Builders;

use QueryMapper\Core\Builders\Base\PDOBuilder;
use QueryMapper\Exceptions\BuilderException;

class PostgreSQLBuilder extends PDOBuilder
{
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_pgsql')) {
                throw new BuilderException("pdo_pgsql extension is required.");
            }
            if (!isset($_ENV['POSTGRESQL_DSN']) || !isset($_ENV['POSTGRESQL_USER']) || !isset($_ENV['POSTGRESQL_PASSWORD'])) {
                throw new BuilderException("PostgreSQL connection parameters are missing. Check the environment variables have loaded correctly.");
            }
            $this->setPDO($this->createPDO($_ENV['POSTGRESQL_DSN'], $_ENV['POSTGRESQL_USER'], $_ENV['POSTGRESQL_PASSWORD']));
        }
    }
}