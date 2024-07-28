<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 08:31
 */


namespace QueryMapper\Core;

use QueryMapper\Interfaces\Arrayable;
use QueryMapper\Interfaces\Jsonable;
use QueryMapper\Traits\Model\ModelInteractions;

abstract class Model implements Arrayable, Jsonable
{
    use ModelInteractions;

    public function toArray(): array
    {
        return $this->getProperties();
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray());
    }
}