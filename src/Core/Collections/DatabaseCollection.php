<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 08:03
 */


namespace QueryMapper\Core\Collections;

class DatabaseCollection implements \Iterator
{
    private array $data = [];
    private int $position = 0;
    private int $rowCount;
    private ?int $lastInsertId;

    public function __construct(array $data, int $rowCount, ?int $lastInsertId)
    {
        $this->data = $data;
        $this->rowCount = $rowCount;
        $this->lastInsertId = $lastInsertId;
    }

    public function __debugInfo(): ?array
    {
        return [
            "rowCount" => $this->rowCount,
            "lastInsertId" => $this->lastInsertId,
            "data" => $this->data,
        ];
    }

    public function current(): mixed
    {
        return $this->data[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }
}