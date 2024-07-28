<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:29
 */

namespace QueryMapper\Enums;

use QueryMapper\Interfaces\IBuilder;
use QueryMapper\Core\Builders\MySQLBuilder;
use QueryMapper\Core\Builders\PostgreSQLBuilder;
use QueryMapper\Core\Builders\SQLiteBuilder;
use QueryMapper\Core\Builders\MSSQLBuilder;

enum DatabaseType: string
{
    case MySQL = 'mysql';
    case PostgreSQL = 'pgsql';
    case SQLite = 'sqlite';
    case MSSQL = 'sqlsrv';

    public function getBuilder(): IBuilder
    {
        return match ($this) {
            self::MySQL => new MySQLBuilder(),
            self::PostgreSQL => new PostgreSQLBuilder(),
            self::SQLite => new SQLiteBuilder(),
            self::MSSQL => new MSSQLBuilder(),
        };
    }
}
