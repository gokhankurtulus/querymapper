<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 12:35
 */


namespace QueryMapper\Loggers;

use Logger\Logger;

class ModelLogger extends Logger
{
    protected static string $folderPath = "";
    protected static string $fileName = "model.log";
}