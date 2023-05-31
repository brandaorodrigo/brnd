# Validate

Contains a set of methods that perform different types of data validation based on specific rules.

## How to use

`Validate::execute(array $data, array $validations, ?array $custom = []): ?string`

The execute method its the expected way to use this class. The validation rules are defined as keys in the rules array, and the corresponding parameter for that rule is the value. For example, if a validation rule is 'max:10', it means that the maximum allowed value for that attribute is 10. Finally will return a string if there is a validation error, and null if all validations pass.

```php
$_POST = [
    'idExample' => 2,
    "datetimeExample" => "2024-04-01T00:00:00-03:00",
    "dateExample" => "2023-04-01",
    "timeExample" => "08:00",
    "cnpjExample" => "25183633000103",
    "cpfExample" => "69703449085",
    "boolExample" => true,
    "floatExample" => 122.22,
    "intExample" => 2,
    "emailExample" => "brnd@brnd.com",
    "urlExample" => "http://www.brnd.com",
    "fullnameExample" => "full name example",
    "inExample" => "yellow",
    "lengthExample" => "rrrr",
    "numericExample" => "22.22",
    "queryExample" => 32
];

$invalid = Validate::execute($_POST, [
    'idExample' => 'required|numeric|in:22,33',
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
    'queryExample' => 'required|query:select top 1 user from employee where user = ?',
    'timeExample' => 'time|after:08:00|before:22:00',
    'urlExample' => 'required|url',
]);

if ($invalid) {
    Page::json(['message' => $invalid], 400);
}
```

## Rules

### Type

- bool
- float
- int
- string
- numeric

### Date

- date
- datetime
- time
- after:**2023-02-10T12:00:00Z**
- after:**2023-02-10**
- after:**12:00:00**
- before:**2023-02-10T12:00:00Z**
- before:**2023-02-10**
- before:**12:00:00**

### Mask

- cnpj
- cpf
- email
- fullname
- url

### Compare

- in:**option1,option2,option2**
- length:**99**
- max:**99**
- min:**1**
- query:**select top 1 1 from customer where id = ?**
