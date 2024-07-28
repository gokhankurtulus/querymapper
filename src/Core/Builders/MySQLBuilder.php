<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 07:19
 */


namespace QueryMapper\Core\Builders;

use QueryMapper\Core\Builders\Base\PDOBuilder;
use QueryMapper\Exceptions\BuilderException;

class MySQLBuilder extends PDOBuilder
{
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_mysql')) {
                throw new BuilderException("pdo_mysql extension is required.");
            }
            if (!isset($_ENV['MYSQL_DSN']) || !isset($_ENV['MYSQL_USER']) || !isset($_ENV['MYSQL_PASSWORD'])) {
                throw new BuilderException("MySQL connection parameters are missing. Check the environment variables have loaded correctly.");
            }
            $this->setPDO($this->createPDO($_ENV['MYSQL_DSN'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}