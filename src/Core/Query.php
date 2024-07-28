<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:40
 */


namespace QueryMapper\Core;

class Query
{
    protected ?string $table = null;
    protected ?string $indexColumn = null;
    protected ?string $indexValue = null;

    protected ?string $operation = null;
    protected array $query = [];
    protected array $bindings = [];

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(?string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function clearTable(): static
    {
        $this->table = null;
        return $this;
    }

    public function getIndexColumn(): ?string
    {
        return $this->indexColumn;
    }

    public function setIndexColumn(?string $indexColumn): static
    {
        $this->indexColumn = $indexColumn;
        return $this;
    }

    public function clearIndexColumn(): static
    {
        $this->indexColumn = null;
        return $this;
    }

    public function getIndexValue(): ?string
    {
        return $this->indexValue;
    }

    public function setIndexValue(?string $indexValue): static
    {
        $this->indexValue = $indexValue;
        return $this;
    }

    public function clearIndexValue(): static
    {
        $this->indexValue = null;
        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(?string $operation): static
    {
        $this->operation = $operation;
        return $this;
    }

    public function clearOperation(): static
    {
        $this->operation = null;
        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): static
    {
        $this->query = $query;
        return $this;
    }

    public function addToQuery(mixed $query): static
    {
        $this->query[] = $query;
        return $this;
    }

    public function clearQuery(): static
    {
        $this->query = [];
        return $this;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function setBindings(array $bindings): static
    {
        $this->bindings = $bindings;
        return $this;
    }

    public function addToBindings(mixed $binding): static
    {
        $this->bindings[] = $binding;
        return $this;
    }

    public function clearBindings(): static
    {
        $this->bindings = [];
        return $this;
    }

    public function clean(): void
    {
        $this->clearTable();
        $this->clearIndexColumn();
        $this->clearIndexValue();
        $this->clearOperation();

        $this->clearQuery();
        $this->clearBindings();
    }
}