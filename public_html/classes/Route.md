# Route

Contains several static methods for handling web page requests and responses.

## How to use

The access routes will search for a file equivalent to the route path.

There are two ways to organize the files in the routes folder. By directories names or by underlines inside the filename.
It is also possible to use both types together, when necessary.

```text
Route:
http://site.com/store/customer/list

File:
./routes/store/customer/list.php
or
./routes/store_customer_list.php
or
./routes/store/customer_list.php
```

## Variables inside the routes

To use variables within routes, use the `$` symbol in the directory name or filename.

```text
Route:
http://site.com/store/customer/88

File:
./routes/store/customer/$id.php
or
./routes/store_customer_$id.php
or
./routes/store/customer_$id.php
```

```text
Route:
http://site.com/store/12/view

File:
./routes/store/$code/view.php
or
./routes/store_$code_view.php
or
./routes/store/$code_view.php
```

## Restrict access to a single method

To restrict the access a route exclusively to a method, add two dashes and the name of the desired method at the end of the file.

```text
Route (Method POST):
http://site.com/store/customer/88

File:
./routes/store/customer/$id--post.php
```

```text
Route (Method DELETE):
http://site.com/store

File:
./routes/store--delete.php
```

## Path

`Route::path(?int $position = null): ?string`

Takes an integer argument position or variable name and returns a string containing a segment of the current URL path. If don't inform a position or name returns the complete current URL path.

```php
$found = Route::path();
// if url is http://site.com/customer/data/ the $found will be 'customer/data'

$found = Route::path(1);
// if url is http://site.com/customer/data/ the $found will be 'customer'

$found = Route::path(2);
// if url is http://site.com/customer/data/ the $found will be 'data'

$found = Route::path('uid');
// if route file is customer_data_$uid.php
// and url is http://site.com/customer/data/12 the $found will be '12'
```

## Headers

`Route::headers(): array`

The headers method returns an associative array containing all HTTP headers in the current request.

```php
$headers = Route::headers();
```

## Body

`Route::body(): array`

The body method reads the request body from the php://input stream and returns it as a decoded array.
It assumes that the body contains JSON data and uses json_decode() to convert it to an array.

```php
$body = Route::body();
```

## Json

`Route::json(array $array, int $code = 200): void`

The json method takes an associative array argument array and an integer argument code and sends a JSON response to the client with the specified HTTP status code. It encodes the array as JSON using json_encode and outputs it using echo.

```php
Route::json(['name' => 'brnd', 'email' => 'brnd@brnd.com'], 403);
```

## Execute

`Route::execute(string  $folder):  void`

The execute method takes a string argument folder and executes a PHP file based on the current URL path and request method. It first extracts the segments of the path using explode() and adds them to a list of possible PHP file names based on the directory specified in folder. It then iterates over this list and includes the first file that exists. If no file is found, it sets the HTTP response code to 404.
_This method need to be executed only one time in the main file of the web site_

```php
Route::execute(__DIR__ . DIRECTORY_SEPARATOR . 'routes');
```
