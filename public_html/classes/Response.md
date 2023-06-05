# Request

Contains several static methods for handling web page requests.

## Json

`Response::json(array $array, int $code = 200): void`

The json method takes an associative array argument array and an integer argument code and sends a JSON response to the client with the specified HTTP status code. It encodes the array as JSON using json_encode and outputs it using echo.

```php
Response::json(['name' => 'brnd', 'email' => 'brnd@brnd.com'], 403);
```

## If Not Found

`Response::ifNotFound(string $table, string $column, string $id): void`

```php
Response::ifNotFound('table', 'column', $id);
```

## If Invalid

`Response::ifInvalid(array $data, array $validations, ?array $custom = []): void`

```php
Response::ifInvalid($request, [
    'value' => 'required|numeric|min:1',
]);
```

## Control

`Response::control(string $query, ?array $params = null): void`

```php
$sql = "SELECT * FROM user";
Response::control($sql);
```
