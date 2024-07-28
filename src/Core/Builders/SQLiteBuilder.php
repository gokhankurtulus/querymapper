<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 07:57
 */


namespace QueryMapper\Core\Builders;

use QueryMapper\Core\Builders\Base\PDOBuilder;
use QueryMapper\Exceptions\BuilderException;

class SQLiteBuilder extends PDOBuilder
{
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_sqlite')) {
                throw new BuilderException("pdo_sqlite extension is required.");
            }
            if (!isset($_ENV['SQLITE_DSN'])) {
                throw new BuilderException("SQLite connection parameters are missing. Check the environment variables have loaded correctly");
            }
            $this->setPDO($this->createPDO($_ENV['SQLITE_DSN']));
        }
    }
}