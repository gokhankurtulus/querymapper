# Model

* [Creating a Model](#creating-a-model)
* [Attributes](#attributes)
    * [Default Database Type](#default-database-type)
    * [Database Type](#database-type)
    * [Custom Builder](#custom-builder)
    * [Public Attribute Methods](#public-attribute-methods)
* [Interactions](#interactions)
    * [Creating Objects](#creating-objects)
    * [Retrieving Objects](#retrieving-objects)
    * [Updating Objects](#updating-objects)
    * [Deleting Objects](#deleting-objects)

## Creating a Model

You can simply create a model by extending the `QueryMapper\Model` class.

```php
use QueryMapper\Core\Model;
use QueryMapper\Enums\DatabaseType;

class User extends Model
{
    protected static ?DatabaseType $databaseType = DatabaseType::MySQL;
    protected static string $table = "users";
    protected static string $indexColumn = "id";
}
```

## Attributes

The following parameters are valid by default for all models.

```php
protected static ?DatabaseType $defaultDatabaseType = null;
protected static ?DatabaseType $databaseType = null;
protected static string $table = '';
protected static string $indexColumn = 'id';
protected mixed $indexValue = '';
protected array $properties = [];
```

* `$table`: The table name for model. You must set this parameter before working with models.

* `$indexColumn`: The column name for the index column. Default value is `id`.

* `$indexValue`: The value of the index column. Default value is empty.
* `$properties`: The properties of the model. Default value is empty.

### Default Database Type

`$defaultDatabaseType`: The default database type for model. If you work with only one database, you can set this parameter before
working with models
to avoid setting it for each model.

```php
Model::setDefaultDatabaseType(DatabaseType::MySQL);
```

### Database Type

`$databaseType`: The database type for specific model. If you work with multiple database types, you can set this parameter directly before
working
with models.

```php
class User extends Model
{
    protected static ?DatabaseType $databaseType = DatabaseType::MySQL;
}
class Post extends Model
{
    protected static ?DatabaseType $databaseType = DatabaseType::PostgreSQL;
}
```

or before using the model.

```php
User::setDatabaseType(DatabaseType::MySQL);
Post::setDatabaseType(DatabaseType::PostgreSQL);

User::find(1);
Post::find(1);
```

### Custom Builder

If you want to create and use custom builder classes for models, you can set builder parameter directly.
This allows you to use your models that are in the same database type but in different sources.
Builder class must extend one of the base builder classes like ` PDOBuilder` which extends from base connections
and implements `IBuilder` interface. Must be set before working with related models.

```php
class CustomBuilder extends PDOBuilder
{
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_mysql')) {
                throw new BuilderException("pdo_mysql extension is required.");
            }
            $this->setPDO($this->createPDO($_ENV['CUSTOM_DSN'], $_ENV['CUSTOM_USER'], $_ENV['CUSTOM_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}

class AnotherBuilderForPosts extends PDOBuilder {
    public function initialize(): void
    {
        if (!$this->getPDO()) {
            if (!extension_loaded('pdo_mysql')) {
                throw new BuilderException("pdo_mysql extension is required.");
            }
            $this->setPDO($this->createPDO($_ENV['ANOTHER_DSN'], $_ENV['ANOTHER_USER'], $_ENV['ANOTHER_PASSWORD']));
            $this->getPDO()?->exec("SET NAMES UTF8");
        }
    }
}

User::setBuilder(new CustomBuilder());
Post::setBuilder(new AnotherBuilderForPosts());
$user = User::find(1);
$posts = Post::where(["author", "=", 1])::get();
```

### Public Attribute Methods

The following methods are available for all models.

* `getDefaultDatabaseType()` : Returns the default database type associated with the model.
* `setDefaultDatabaseType($databaseType)` : Sets the default database type associated with the model.
* `getDatabaseType()` : Returns the database type associated with the model.
* `setDatabaseType($databaseType)` : Sets the database type associated with the model.
* `getBuilder()` : Returns the builder associated with the model.
* `setBuilder($builder)` : Sets the builder associated with the model.


* `getTable()` : Returns the table name associated with the model.
* `getIndexColumn()` : Returns the index column name associated with the model.
* `getIndexValue()` : Returns the index value associated with the model.
* `setIndexValue($value)` : Sets the index value associated with the model.
* `getProperties()` : Returns the properties associated with the model.
* `setProperties($properties)` : Sets the properties associated with the model.
* `getProperty($key)` : Returns the property value associated with the key.
* `setProperty($key, $value)` : Sets the property value associated with the key.
* `assignProperties($properties)` : Assigns the properties to the model.
* `getInstance()` : Returns the instance of the model.
* `isEmpty()` : Checks if the model is empty.
* `hasDiff($properties, $returnDiff = false)` : Checks if the model has differences with the given properties.


* `toArray()` : Converts the model properties to an array.
* `toJson()` : Converts the model properties to a JSON string.

## Interactions

### Creating Objects

You can create new object using the `create` method. The method accepts an array for fields.

```php
$user = User::create([
    "name" => "John Doe",
    "email" => "john@doe.com",
]);

var_dump($user); // User object id
```

### Retrieving Objects

To retrieve a single object, you can use the `find` method. It accepts three parameter pairs and returns the `ModelCollection` of the model.

Parameter pairs;

* find($value) - Search for model's indexColumn = $value
* find($value, $key) - Search for $key = $value
* find($value, $key, $operator) - Search for $key (>, >=, =, !=, <, <=) $value

```php
$user = User::find(123); // Get the user for indexColumn (id) is '123'
// Or you can search for specific keys
$user = User::find('johndoe', 'username'); // Get the user for username is 'johndoe'
$user = User::find(1, 'status', '>='); // Get the user for status is greater than or equal to 1
```

There are several ways to get objects. You can build queries by chaining the methods.

The `select`,`where`,`orWhere` methods are starter methods;

The `get(1)` method returns `ModelCollection` with single object, while `get()` or `get(int $limit)` returns `ModelCollection` with multiple
objects.
If there is no result returns `ModelCollection` with empty data.

```php
// Retrieving all records, all these ways are valid and will do the same thing
$users = User::get();
$users = User::select()::get();
$users = User::where()::get();
$users = User::select()::where()::get();

if (!$users->isEmpty()) {
//    foreach ($users as $user) {
//        var_dump($user->getProperties());
//    }
//    $arr = $users->toArray();
//    $json = $users->toJson();
}

// Retrieving first records
$firstRecord = User::get(1);
$firstRecord = User::where(['status', '=', 1])::get(1);

if (!$firstRecord->isEmpty()) {
//    var_dump($firstRecord->current->getProperties());
}

// Retrieving specific amount of records
$users = User::get(10);
$users = User::where(['status', '=', 1])::get(10);

if (!$users->isEmpty()) {
//    foreach ($users as $user) {
//        var_dump($user->getProperties());
//    }
}

// Retrieving only first records id property where status = 1
User::select(['id'])::where(['status', '=', '1'])::get(1);
if (!$firstRecord->isEmpty()) {
//    var_dump($firstRecord->current->getProperty('id')); // 111
//    var_dump($firstRecord->current->getProperties()); // array(1) { ["id"]=> int(111) }
}

// Retrieving only name, lastname where created_at > 2023-01-01
User::select(['name', 'lastname'])::where(['created_at', '>', '2023-01-01'])::get();

// This will look like; where role = 1 OR status = 1
$users = User::where(['role', '=', 1])
    ::orWhere(['status', '=', 1])
    ::get();

// When you give multiple arrays to "where" and "orWhere" methods they automatically groups
// This will look like; where (role = 1 AND verification_status = 1)
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])::get();

// This will look like; where (role = 1 AND verification_status = 1) AND (status = 1 AND verification_status = 1)
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])
    ::where(['status', '=', 1], ['verification_status', '=', 1])
    ::get();

// This will look like; where (role = 1 AND verification_status = 1) OR (status = 1 AND verification_status = 1)
$users = User::where(['role', '=', 1], ['verification_status', '=', 1])
    ::orWhere(['status', '=', 1], ['verification_status', '=', 1])
    ::get();
```

### Updating Objects

The `update` and `updateMany` methods can be using for updating objects. `updateMany` method accepts an array as parameters.

`update` and `set` methods executes the query and returns the affected record count.

```php
$user = User::find('1');
if (!$user->isEmpty()) {
//    $fields = [
//        'id' => '1',
//        'name' => 'John Doe',
//        'email' => 'john@doe.com',
//        'status' => '1',
//    ]
//    if ($user->current->hasDiff($fields)) {
//        $result = $user->current->update($fields);
//    }
    $result = $user->current->update(['status' => '1']);
    
    var_dump($result); // 1
}

// This will look like; SET status = 1 WHERE status != 1
$result = User::updateMany(['status' => '1'])::where(['status', '!=', '1'])::set();
// This will look like; SET status = 1 WHERE status != 1 OR id = 1
$result = User::updateMany(['status' => '1'])::where(['status', '!=', '1'])::orWhere(['id', '=', '1'])::set();
```

### Deleting Objects

To delete objects, you can use `delete` and `deleteMany` method.

`delete` and `remove` methods executes the query and returns the affected record count.

```php
$user = User::find(1);
if (!$user->isEmpty()) {
    $result = $user->current->delete();
    var_dump($result); // 1
}

// This will look like; where created at < 2023-01-01 or deleted = 1
$user = User::deleteMany()::where(['created_at', '<', '2023-01-01'])::orWhere(['deleted','=','1'])::remove();
```
