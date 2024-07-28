<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 27.07.2024 Time: 09:56
 */

namespace QueryMapper\Traits\Model;

use QueryMapper\Enums\DatabaseType;
use QueryMapper\Exceptions\ModelException;
use QueryMapper\Interfaces\IBuilder;
use QueryMapper\Loggers\ModelLogger;

trait ModelAttributes
{
    private static array $builders = [];
    protected static ?DatabaseType $defaultDatabaseType = null;
    protected static ?DatabaseType $databaseType = null;
    protected static string $table = '';
    protected static string $indexColumn = 'id';
    protected mixed $indexValue = '';
    protected array $properties = [];

    final public static function getDefaultDatabaseType(): ?DatabaseType
    {
        return static::$defaultDatabaseType;
    }

    final public static function setDefaultDatabaseType(?DatabaseType $defaultDatabaseType): void
    {
        static::$defaultDatabaseType = $defaultDatabaseType;
    }

    final public static function getDatabaseType(): ?DatabaseType
    {
        return static::$databaseType;
    }

    final public static function setDatabaseType(?DatabaseType $databaseType): void
    {
        static::$databaseType = $databaseType;
    }

    final protected static function configure(): void
    {
        if (!static::getDatabaseType()) {
            static::setDatabaseType(static::getDefaultDatabaseType());
        }
        if (!static::getDatabaseType() instanceof DatabaseType) {
            ModelLogger::log("Failed to configure database driver. Model's driver is not instance of DatabaseDriver");
            throw new ModelException("Failed to configure database driver. Model's driver is not instance of DatabaseDriver");
        }
        $builder = static::getDatabaseType()?->getBuilder();
        if (!$builder instanceof IBuilder) {
            ModelLogger::log("Failed to configure database driver. Model's builder is not instance of IBuilder");
            throw new ModelException("Failed to configure database driver. Model's builder is not instance of IBuilder");
        }
        static::setBuilder($builder);
    }

    final public static function getBuilder(): IBuilder
    {
        if (!isset(static::$builders[static::class])) {
            static::configure();
        }
        return static::$builders[static::class];
    }

    final public static function setBuilder(IBuilder $builder): void
    {
        static::$builders[static::class] = $builder;
    }

    final public static function getTable(): string
    {
        return static::$table;
    }

    final public static function getIndexColumn(): string
    {
        return static::$indexColumn;
    }

    final public function getIndexValue(): mixed
    {
        return $this->indexValue;
    }

    final protected function setIndexValue(mixed $indexValue): void
    {
        $this->indexValue = $indexValue;
    }

    final public function getProperties(): array
    {
        return $this->properties;
    }

    final public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    final public function getProperty(string $key): mixed
    {
        return $this->properties[$key] ?? null;
    }

    final public function setProperty(string $key, mixed $value): void
    {
        $this->properties[$key] = $value;
    }

    final protected function assignProperties(array|object $entityProperties = []): void
    {
        foreach ($entityProperties as $entityKey => $entityValue) {
            $this->setProperty($entityKey, $entityValue);
            if ($entityKey === static::getIndexColumn()) {
                $this->setIndexValue($entityValue);
            }
        }
    }

    final public static function getInstance(): static
    {
        return new static();
    }

    final public function isEmpty(): bool
    {
        return empty($this->getProperties());
    }

    final public function hasDiff(array $fields, bool $returnDiff = false): array|bool
    {
        $diff = [];
        foreach ($fields as $key => $field) {
            if ($this->getProperty($key) != $field) {
                $diff[$key] = $field;
            }
        }
        return $returnDiff ? $diff : !empty($diff);
    }
}