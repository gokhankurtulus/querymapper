<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 08:31
 */

namespace QueryMapper\Interfaces;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}