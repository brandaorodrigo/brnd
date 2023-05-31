<?php

/*

curl --location 'http://localhost/example/validate' \
--header 'Content-Type: application/json' \
--data-raw '{
    "datetimeExample": "2024-04-01T00:00:00-03:00",
    "dateExample": "2023-04-01",
    "timeExample": "08:00",
    "cnpjExample": "25183633000103",
    "cpfExample": "69703449085",
    "boolExample": true,
    "floatExample": 122.22,
    "intExample": 2,
    "emailExample": "email@email.com",
    "urlExample": "http://www.site.com",
    "fullnameExample": "full name example",
    "inExample": "yellow",
    "lengthExample": "rrrr",
    "numericExample": "22.22",
    "queryExample": 32
}'

*/

$invalid = Validate::execute($_POST, [
    'boolExample' => 'bool',
    'cnpjExample' => 'required|cnpj',
    'cpfExample' => 'required|cpf',
    'dateExample' => 'required|date|before:' . date('Y-m-d'),
    'datetimeExample' => 'required|datetime|after:' . date('c'),
    'emailExample' => 'required|email',
    'floatExample' => 'required|float',
    'fullnameExample' => 'required|fullname',
    'inExample' => 'required|in:yellow,gree,blue,red',
    'intExample' => 'required|int|min:2|max:9',
    'lengthExample' => 'required|length:4',
    'numericExample' => 'required|numeric',
    'queryExample' => 'required|query:select top 1 ctr_usu_nome from ctr_usuarios where ctr_usu_nome = ?',
    'timeExample' => 'time|after:08:00|before:22:00',
    'urlExample' => 'required|url',
]);

if ($invalid) {
    Route::json(['message' => $invalid], 400);
}

Route::json([], 204);
