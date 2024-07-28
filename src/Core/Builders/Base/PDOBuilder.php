<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 06:56
 */

namespace QueryMapper\Core\Builders\Base;

use QueryMapper\Core\Collections\DatabaseCollection;
use QueryMapper\Core\Connections\PDOConnection;
use QueryMapper\Exceptions\BuilderException;
use QueryMapper\Loggers\PDOLogger;

abstract class PDOBuilder extends PDOConnection implements \QueryMapper\Interfaces\IBuilder
{
    abstract public function initialize(): void;

    public function manipulateComparisonOperator(string $operator): string
    {
        $operatorMap = [
            '=' => '=',
            '!=' => '!=',
            '<>' => '!=',
            '>' => '>',
            '>=' => '>=',
            '<' => '<',
            '<=' => '<=',
        ];

        return $operatorMap[$operator] ?? '=';
    }

    public function count(array $fields = ['*']): static
    {
        $this->select(['COUNT(' . implode(', ', $fields) . ')']);
        return $this;
    }

    public function select(array $fields = ['*']): static
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setOperation('read');
        $this->setQuery(["SELECT " . implode(', ', $fields)]);
        return $this;
    }

    public function from(string $table): static
    {
        $this->setTable($table);
        $this->addToQuery(" FROM {$table}");
        return $this;
    }

    public function as(string $as): static
    {
        $this->addToQuery(" AS $as ");
        return $this;
    }

    public function innerJoin(string $table, string $on): static
    {
        $this->addToQuery(" INNER JOIN $table ON {$on} ");
        return $this;
    }

    public function leftJoin(string $table, string $on): static
    {
        $this->addToQuery(" LEFT JOIN $table ON {$on} ");
        return $this;
    }

    public function rightJoin(string $table, string $on): static
    {
        $this->addToQuery(" RIGHT JOIN $table ON {$on} ");
        return $this;
    }

    public function fullJoin(string $table, string $on): static
    {
        $this->addToQuery(" FULL OUTER JOIN $table ON {$on} ");
        return $this;
    }

    public function where(array ...$args): static
    {
        $this->position("AND", ...$args);
        return $this;
    }

    public function orWhere(array ...$args): static
    {
        $this->position("OR", ...$args);
        return $this;
    }

    public function position(string $logicalOperator, array ...$args): void
    {
        if (empty($args) || (count($args) === 1 && empty($args[0]))) {
            return;
        }
        $clause = "";

        if (count($args) > 1) {
            $clause .= "(";
        }

        foreach ($args as $index => $condition) {
            if (count($condition) !== 3) {
                throw new BuilderException("Invalid where condition. Expected 3 elements in the array.");
            }
            [$field, $operator, $value] = $condition;
            if ($index > 0) {
                $clause .= " AND ";
            }
            $clause .= "{$field} {$this->manipulateComparisonOperator($operator)} ?";
            $this->addToBindings($value);
        }
        if (count($args) > 1) {
            $clause .= ")";
        }
        $hasWhere = false;
        foreach ($this->getQuery() as $queryLine) {
            if (str_contains($queryLine, "WHERE")) {
                $hasWhere = true;
                break;
            }
        }
        $this->addToQuery((!$hasWhere ? " WHERE " : " {$logicalOperator} ") . "{$clause}");
    }

    public function orderBy(array ...$args): static
    {
        $clause = '';
        $multipleOrderClauses = count($args) > 1;
        $lastItem = array_key_last($args);
        foreach ($args as $index => $sort) {
            if (count($sort) !== 1 && count($sort) !== 2) {
                throw new BuilderException("Invalid order by condition. Expected 1 or 2 elements in the array.");
            }
            @[$field, $direction] = $sort;
            $clause .= " {$field} {$direction} " . ($multipleOrderClauses && $index !== $lastItem ? ',' : '');
        }
        $hasOrder = false;
        foreach ($this->getQuery() as $queryLine) {
            if (str_contains($queryLine, "ORDER BY")) {
                $hasOrder = true;
                break;
            }
        }
        $this->addToQuery(!$hasOrder ? " ORDER BY {$clause} " : " {$clause} ");
        return $this;
    }

    public function limit(?int $limit, ?int $offset = 0): static
    {
        if (!is_null($limit)) {
            $this->addToQuery(" LIMIT {$limit}");
        }
        if (!is_null($offset)) {
            $this->addToQuery(" OFFSET {$offset}");
        }
        return $this;
    }

    public function insert(string $table): static
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setOperation('create');
        $this->setQuery(["INSERT INTO {$table} "]);
        return $this;
    }

    public function values(array $values): static
    {
        $fields = array_keys($values);
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->setBindings(array_values($values));
        $this->addToQuery("(" . implode(', ', $fields) . ") VALUES ($placeholders)");
        return $this;
    }

    public function update(string $table): static
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setOperation('update');
        $this->setQuery(["UPDATE {$table} "]);
        return $this;
    }

    public function set(array $values): static
    {
        $set = [];
        foreach ($values as $field => $value) {
            $this->addToBindings($value);
            $set[] = "{$field} = ?";
        }
        $this->addToQuery(" SET " . implode(', ', $set) . " ");
        return $this;
    }

    public function delete(): static
    {
        $this->clearQuery();
        $this->clearBindings();
        $this->setOperation('delete');
        $this->setQuery(["DELETE "]);
        return $this;
    }

    public function build(): DatabaseCollection
    {
        try {
            $this->initialize();
            $this->getPDO()->beginTransaction();
            $statement = $this->getPDO()->prepare(implode(' ', $this->getQuery()));
            $statement->execute($this->getBindings());

            $data = $statement->fetchAll();
            $rowCount = $statement->rowCount();
            $lastInsertId = $this->getOperation() == 'create' ? $this->getPDO()->lastInsertId() : null;

            if ($this->getPDO()->inTransaction()) {
                $this->getPDO()->commit();
            }
            $this->clearQuery();
            $this->clearBindings();
            $this->setOperation(null);
            return new DatabaseCollection($data, $rowCount, $lastInsertId);
        } catch (\Throwable $exception) {
            $query = implode('', $this->getQuery());
            $bindings = implode('', $this->getBindings());
            $message = "Exception: {$exception->getMessage()}\r\nQuery: {$query}\r\nBindings: {$bindings}";
            if ($this->getPDO()?->inTransaction()) {
                $this->getPDO()->rollBack();
            }
            PDOLogger::log($message);
            throw new BuilderException($exception->getMessage());
        }
    }
}