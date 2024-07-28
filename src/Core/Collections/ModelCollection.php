<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 12:32
 */


namespace QueryMapper\Core\Collections;

use QueryMapper\Core\Model;
use QueryMapper\Interfaces\Arrayable;
use QueryMapper\Interfaces\Jsonable;

class ModelCollection implements \Iterator, \Countable, Arrayable, Jsonable
{
    private array $data = [];
    private int $position = 0;
    private string $table;

    public function __construct(iterable $data, string $table)
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif ($data instanceof \Traversable) {
            $this->data = iterator_to_array($data);
        } else {
            throw new \InvalidArgumentException("Invalid data type. It should be an array or Traversable.");
        }
        $this->table = $table;
        $this->rewind();
    }

    public function __debugInfo(): ?array
    {
        return [
            "table" => $this->table,
            "data" => $this->data,
        ];
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Model|null
    {
        return $this->data[$this->position] ?? null;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function append(Model $item): void
    {
        $this->data[] = $item;
    }

    /**
     * @return Model[]
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function toJson(int $options = 0): string
    {
        $jsonData = [];
        foreach ($this->data as $item) {
            $jsonData[] = $item->toArray();
        }
        return json_encode($jsonData, $options);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}