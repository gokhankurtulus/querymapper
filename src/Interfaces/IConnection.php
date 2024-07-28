<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:39
 */

namespace QueryMapper\Interfaces;

interface IConnection
{
    public function initialize(): void;

    public function terminate(): void;

    public function getTable(): ?string;

    public function setTable(?string $table): static;

    public function clearTable(): static;

    public function getIndexColumn(): ?string;

    public function setIndexColumn(?string $indexColumn): static;

    public function clearIndexColumn(): static;

    public function getIndexValue(): ?string;

    public function setIndexValue(?string $indexValue): static;

    public function clearIndexValue(): static;

    public function getOperation(): ?string;

    public function setOperation(?string $operation): static;

    public function clearOperation(): static;

    public function getQuery(): array;

    public function setQuery(array $query): static;

    public function addToQuery(mixed $query): static;

    public function clearQuery(): static;

    public function getBindings(): array;

    public function setBindings(array $bindings): static;

    public function addToBindings(mixed $binding): static;

    public function clearBindings(): static;
}