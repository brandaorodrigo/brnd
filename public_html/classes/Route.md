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

Takes an integer argument position and returns a string containing a segment of the current URL path. If don't inform a position returns the complete current URL path.

```php
$found = Route::path();
// if url is http://site.com/customer/data/ the $found will be 'customer/data'

$found = Route::path(1);
// if url is http://site.com/customer/data/ the $found will be 'customer'

$found = Route::path(2);
// if url is http://site.com/customer/data/ the $found will be 'data'
```

## Execute

`Route::execute(string  $folder):  string`

The execute method takes a string argument folder and executes a PHP file based on the current URL path and request method. It first extracts the segments of the path using explode() and adds them to a list of possible PHP file names based on the directory specified in folder. It then iterates over this list and includes the first file that exists. If no file is found, it sets the HTTP response code to 404.
_This method need to be executed only one time in the main file of the web site_

```php
include Route::execute(__DIR__ . DIRECTORY_SEPARATOR . 'routes');
```
