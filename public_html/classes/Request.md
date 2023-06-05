# Request

Contains several static methods for handling web page requests.

## Body

`Request::body(): array`

The body method reads the request body from the php://input stream and returns it as a decoded array.
It assumes that the body contains JSON data and uses json_decode() to convert it to an array.

```php
$body = Request::body();
```

## Headers

`Request::headers(): array`

The headers method returns an associative array containing all HTTP headers in the current request.

```php
$headers = Request::headers();
```

## All

`Request::all(): array`

The All method returns all the possible values at the same time. Concatenates `$_POST`, `$_GET`, `Headers` and `Body`.

```php
$all = Request::all();
```

## Value

`Request::value(string $string): string`

The Value method returns the value related to the parameter.

```php
$values = Request::headers();
```
