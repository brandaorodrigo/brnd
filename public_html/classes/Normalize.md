# Normalize

Provides various methods to filter and transform strings based on different rules.

## How to use

`Normalize::execute(array $data, array $filters): array`

The execute method its the expected way to use this class. It's takes an array containing arrays or strings as input and returns transformed data based on the filter rules applied to the input data. In addition, some rules accept an optional parameter that specifies the output format. Each parameter's rules must be separated by the `|` symbol.

```php
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
```

## Another way to use this class

`Normalize::[[filter]](string|float|int $value, ?string $param): string|float|int`

There is an another way to use this class. If is necessary only format a single data, you can call the class specifying the rule.

```php
$output = Normalize::bool('1');
// $output = true;
$output = Normalize::dateConvert('2022-01-02', 'Y-m-d,d/m/Y');
// $output = '02/01/2023';
$value = 'Blink 182';
$output = Normalize::alpha($value);
// $output = 'Blink ';
$output = Normalize::number($value);
// $output = '182';
$output = Normalize::trim(Normalize::alpha($value));
// $output = 'Blink';
$output = Normalize::prefix(Normalize::currency(14.12), 'R$ ');
// $output = 'R$ 14,12';
```

## Rules

### Type

- bool
- float
- int
- none
- string

### Format

- currency
- number
- date:**d/m/Y**
- dateConvert:**Y-m-d,d/m/Y**

### Replace

- alpha
- alphanumeric
- numeric
- trim
- charset:**utf-8,iso-8859-1**

### Mask

- mask:**###.###.###-##**
- cnpj
- cpf
- zipcode

### Case

- lower
- upper
- title
- sentence

### Add

- prefix:**R$**
- sufix:**mins**
- zeros:**10**

### Encrypt

- decode:**key_value**
- encode:**key_value**

## Helpers

This class has a set of extra functions that can be used outside the normalization scheme, but which will help convert and fit data.

- filter
- imageBase64
- implode
