<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 07:51
 */


namespace QueryMapper\Loggers;

use Logger\Logger;

class PDOLogger extends Logger
{
    protected static string $folderPath = "";
    protected static string $fileName = "pdo.log";
}