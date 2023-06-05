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

Route::post('example/validate', function () {
    $request = Request::all();

    $message = Validate::execute($request, [
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

    if ($message) {
        Response::json(['message' => $message], 400);
    }

    Response::json(null, 204);
});

/*

curl --location --request GET 'http://localhost:88/example/normalize' \
--header 'Content-Type: application/json' \

*/

Route::get('example/normalize', function () {
    $rows = [
        [
            'alphaExample' => 'Only123 Characters W111IL-- be f#@ilter3333',
            'alphanumericExample' => 'Here will be filtered NUMBERS 123 and chars. 5512',
            'boolExample' => 'a',
            'charsetExample' => 'PalhaÃ§o',
            'cnpjExample' => '25183633000103',
            'cpfExample' => '69703449085',
            'currencyExample' => '122.22',
            'dateExample' => '2023-04-01',
            'dateconvertExample' => '12/05/2011',
            'datetimeExample' => '2026-04-01T00:00:00-03:00',
            'decodeExample' => '487mzGatKaYmdvK1nmpWeuLhay1yTzlC8ibBWZRIRd3RdUq0MVRw0KdwYU7c5tgbbgXZ8TBRD7yECxjjDmxwmPgwhg6yhnn6yhnn',
            'encodeExample' => 'encodethis',
            'floatExample' => '122.22',
            'intExample' => '66',
            'numberExample' => '1.500.200',
            'numericExample' => 'Here will be filtered NUMBERS 123 and chars. 5512',
            'timeExample' => '08:00',
            'titleExample' => 'Here will be filtered NUMBERS 123 and chars. 5512',
            'upperExample' => 'convert ALL to Uppercase.',
            'zerosExample' => 14,
            'zipcodeExample' => '36025190',
        ]
    ];

    $rows = Normalize::execute($rows, [
        'alphaExample' => 'alpha|sentence',
        'alphanumericExample' => 'alphanumeric|lower',
        'boolExample' => 'bool',
        'charsetExample' => 'charset:iso-8859-1,utf-8',
        'cnpjExample' => 'cnpj',
        'cpfExample' => 'cpf',
        'currencyExample' => 'currency|prefix:R$ ',
        'dateExample' => 'date:d/m/Y',
        'dateconvertExample' => 'dateConvert:d/m/Y,Y-m-d',
        'datetimeExample' => 'date:d/m/Y \à\s H:i',
        'decodeExample' => 'decode:key_example1',
        'encodeExample' => 'encode:key_example1',
        'floatExample' => 'float',
        'maskExample' => 'mask:##-##-##',
        'numberExample' => 'numeric|number|sufix: pessoas',
        'numericExample' => 'numeric|int',
        'timeExample' => 'date:H:i|sufix: horas',
        'titleExample' => 'alpha|title|trim',
        'upperExample' => 'upper',
        'zerosExample' => 'zeros:10',
        'zipcodeExample' => 'zipcode',
    ]);

    Response::json($rows);
});
