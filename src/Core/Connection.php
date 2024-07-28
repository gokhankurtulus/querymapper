<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:38
 */


namespace QueryMapper\Core;

abstract class Connection implements \QueryMapper\Interfaces\IConnection
{
    protected Query $query;

    abstract public function initialize(): void;

    abstract public function terminate(): void;

    public function __construct()
    {
        $this->query = new Query();
    }

    public function getTable(): ?string
    {
        return $this->query->getTable();
    }

    public function setTable(?string $table): static
    {
        $this->query->setTable($table);
        return $this;
    }

    public function clearTable(): static
    {
        $this->query->clearTable();
        return $this;
    }

    public function getIndexColumn(): ?string
    {
        return $this->query->getIndexColumn();
    }

    public function setIndexColumn(?string $indexColumn): static
    {
        $this->query->setIndexColumn($indexColumn);
        return $this;
    }

    public function clearIndexColumn(): static
    {
        $this->query->clearIndexColumn();
        return $this;
    }

    public function getIndexValue(): ?string
    {
        return $this->query->getIndexValue();
    }

    public function setIndexValue(?string $indexValue): static
    {
        $this->query->setIndexValue($indexValue);
        return $this;
    }

    public function clearIndexValue(): static
    {
        $this->query->clearIndexValue();
        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->query->getOperation();
    }

    public function setOperation(?string $operation): static
    {
        $this->query->setOperation($operation);
        return $this;
    }

    public function clearOperation(): static
    {
        $this->query->clearOperation();
        return $this;
    }

    public function getQuery(): array
    {
        return $this->query->getQuery();
    }

    public function setQuery(array $query): static
    {
        $this->query->setQuery($query);
        return $this;
    }

    public function addToQuery(mixed $query): static
    {
        $this->query->addToQuery($query);
        return $this;
    }

    public function clearQuery(): static
    {
        $this->query->clearQuery();
        return $this;
    }

    public function getBindings(): array
    {
        return $this->query->getBindings();
    }

    public function setBindings(array $bindings): static
    {
        $this->query->setBindings($bindings);
        return $this;
    }

    public function addToBindings(mixed $binding): static
    {
        $this->query->addToBindings($binding);
        return $this;
    }

    public function clearBindings(): static
    {
        $this->query->clearBindings();
        return $this;
    }
}