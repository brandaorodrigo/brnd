# brnd framework

## Files

```diff
#├── .editorconfig
#├── .gitignore
#├── .vscode
#|   └──settings.json
@├── LICENSE
@├── README.md
@└── public_html
@    ├── classes
@    │   ├── DB.md
@    │   ├── DB.php
@    |   ├── Normalize.md
@    │   ├── Normalize.php
@    │   ├── Request.md
@    │   ├── Request.php
@    │   ├── Response.md
@    │   ├── Response.php
@    │   ├── Route.md
@    │   ├── Route.php
@    |   ├── Validate.md
@    │   └── Validate.php
+    └── routes
-    |   └── example.php
@    ├── .htaccess
+    ├── app.php
-    ├── config.example.php
@    ├── index.php
@    ├── robot.txt
@    └── web.config
```

## Getting started

Copy the file `config.example.php` and rename to `config.php`.
The `index.php` file will handle identifying which route is being opened and locating the file associated with this route.

### Using Apache

If an Apache server is being used, the `.htaccess` file will be consulted by the server and will redirect all calls to the `index.php` file.

### Using IIS

If an IIS server is being used, the `web.config` file will be consulted by the server and will redirect all calls to the `index.php` file.

### Using Nginx

If an Nginx server is being used, add to the configuration the redirection of all calls to the index.php file.
_The same configuration that is required for the use of Laravel can be done for this framework._

```text
autoindex off;

location / {
    rewrite ^(.*)$ /%1 redirect;
    if (!-e $request_filename) {
        rewrite ^(.*)$ /%1 redirect;
    }
    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php break;
    }
}
```

## Classes

- [DB](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/DB.md)
- [Normalize](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/Normalize.md)
- [Request](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/Request.md)
- [Response](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/Response.md)
- [Route](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/Route.md)
- [Validate](https://github.com/brandaorodrigo/brnd/blob/master/public_html/classes/Validate.md)

## License

Open source software licensed as [MIT](/LICENSE) by [@brandaorodrigo](https://github.com/brandaorodrigo).
