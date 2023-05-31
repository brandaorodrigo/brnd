<?php

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

Route::json($rows);
