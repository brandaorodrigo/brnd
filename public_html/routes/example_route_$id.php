<?php

/*

curl --location --request GET 'http://localhost:88/example/route/44' \
--header 'Content-Type: application/json' \
--data-raw '{
    "emailExample": "brnd@brnd.com",
    "urlExample": "http://www.brnd.com"
}'

*/

$headers = Route::headers();

$body = Route::body();

$path = Route::path();

$params = [Route::path(1), Route::path(2), Route::path(3), Route::path('id')];

Route::json([
    'path' => $path,
    'params' => $params,
    'headers' => $headers,
    'body' => $body
]);
