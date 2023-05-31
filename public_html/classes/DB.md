# DB

Provides a set of static methods for working with a database using the PDO library (SQL Server).

## Select

`DB::select(string $query, ?array $params): array`

Executes a select statement on the database and returns an array of associative arrays representing the rows in the result set.

```php
$rows = DB::select("select * from customer where id = ?", [1]);
```

## Get only the first line

`DB::first(string $query, ?array $params): array`

Executes a select statement on the database and returns the first row in the result set as an associative array.

```php
$row = DB::first("select top 1 * from customer where id = ? and name like ?", [1, 'brnd%']);
```

## Get only the first value

`DB::value(string $query, ?array $params = null): ?string`

Executes a select statement on the database and returns the first value of the first row in the result set and returns like a string.

```php
$email = DB::value("select email from customer where id = ? and name like ?", [1, 'brnd%']);
```

## Insert

`DB::insert(string $table, array $array): void`

Inserts a new row into the specified table with the given values.
_The **lastInsertId** function can be used to returns the last inserted row id from the database._

```php
DB::insert("customer", ['name' => 'brnd', 'email' => 'brnd@brnd.com']);
$id = DB::lastInsertId();
```

## Update

`DB::update(string $table, array $array, string $column, int $id): void`

Updates a row in the specified table with the given values.

```php
DB::update("customer", ['name' => 'brnd', 'email' => 'brnd@brnd.com'], 'id', 1);
```

## Delete

`DB::delete(string $table, string $column, int $id): void`

Deletes a row from the specified table.
_This method execute a logical delete._

```php
DB::delete("customer", 'id', 1);
```

## Procedure

`DB::procedure(string $procedure, array $array): array`

Executes a stored procedure with parameters and returns the result as an array.

```php
$rows = DB::procedure('sp_procedure_name', [
    'id' => 33,
    'name' => 'brnd',
    'exitcode' => 1
]);
```
