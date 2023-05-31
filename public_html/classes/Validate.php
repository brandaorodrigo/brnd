<?php

class Validate
{
    // validations / date ------------------------------------------------------

    private static function date($value, string $rule): bool
    {
        $date = self::createDate($value, $rule);
        if (!$date) {
            return false;
        }
        $formatted = str_replace('+00:00', 'Z', $date->format(self::$format[$rule]));
        return $formatted === $value ? true : false;
    }

    // validations / mask ------------------------------------------------------

    private static function cnpj(string $value): bool
    {
        if (strlen($value) != 14) {
            return false;
        }
        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
            $sum += $value[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $diff = $sum % 11;
        if ($value[12] != ($diff < 2 ? 0 : 11 - $diff)) {
            return false;
        }
        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
            $sum += $value[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $diff = $sum % 11;
        return $value[13] == ($diff < 2 ? 0 : 11 - $diff);
    }

    private static function cpf(string $value): bool
    {
        if (strlen($value) != 11) {
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $value)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $value[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($value[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    private static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    private static function fullname(string $value): bool
    {
        $explode = explode(' ', $value);
        if (count($explode) < 2) {
            return false;
        }
        foreach ($explode as $e) {
            if (strlen($e) === 1) {
                return false;
            }
        }
        return true;
    }

    private static function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) ? true : false;
    }

    // validations / compare ---------------------------------------------------

    private static function in(string $value, string $param): bool
    {
        return in_array($value, explode(',', $param)) ? true : false;
    }

    private static function length(string $value, string $param): bool
    {
        return strlen($value) == $param ? true : false;
    }

    private static function max(int $value, string $param): bool
    {
        return is_numeric($value) && $value <= $param ? true : false;
    }

    private static function min(int $value, string $param): bool
    {
        return is_numeric($value) && $value >= $param ? true : false;
    }

    private static function query(string $value, string $param): bool
    {
        return DB::value($param, [$value]) ? true : false;
    }

    // execute -----------------------------------------------------------------

    public static function execute(array $data, array $validations, ?array $custom = []): ?string
    {
        $scheme = self::scheme($validations);

        foreach ($scheme as $attribute => $rules) {
            $value = @$data[$attribute];

            foreach ($rules as $rule => $param) {

                if ($rule === 'required') {
                    if ($value !== false && (!is_array($value) && !is_object($value) && trim((string) $value) === '')) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    }
                    continue;
                }

                if (!$value && $value !== false && $value !== 0) {
                    continue;
                }

                if (in_array($rule, [
                    'bool',
                    'float',
                    'int',
                    'numeric',
                ])) {
                    $function = 'is_' . $rule;
                    if (!$function($value)) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    }
                    continue;
                }

                if (in_array($rule, [
                    'cnpj',
                    'cpf',
                    'email',
                    'fullname',
                    'url',
                ])) {
                    if (!self::$rule($value)) {
                        return self::message($attribute, $value, $rule, $custom);
                    }
                    continue;
                }

                if (in_array($rule, [
                    'in',
                    'length',
                    'max',
                    'min',
                    'query',
                ])) {
                    if (!$param) {
                        throw new \Exception('Required parameter.');
                    }
                    if (!self::$rule($value, $param)) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    }
                    continue;
                }

                if (in_array($rule, [
                    'datetime',
                    'date',
                    'time'
                ])) {
                    if (!self::date($value, $rule)) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    };
                    continue;
                }

                if (in_array($rule, [
                    'before',
                    'after'
                ])) {
                    $found = null;
                    $keys = array_keys($rules);
                    if (in_array('datetime', $keys)) {
                        $found = 'datetime';
                    }
                    if (in_array('date', $keys)) {
                        $found = 'date';
                    }
                    if (in_array('time', $keys)) {
                        $found = 'time';
                    }
                    if (!$found) {
                        throw new \Exception($rule . ' requires other rules.');
                    }
                    $date = self::createDate($value, $found);
                    $compare = self::createDate($param, $found);
                    if ($rule === 'after' && $date < $compare) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    }
                    if ($rule === 'before' && $date > $compare) {
                        return self::message($attribute, $value, $rule, $custom, $param);
                    }
                    continue;
                }

                throw new \Exception($rule . ' does not exists.');
            }
        }
        return null;
    }

    // utils -------------------------------------------------------------------

    private static $format = ['datetime' => \DateTime::ATOM, 'date' => 'Y-m-d', 'time' => 'H:i'];

    private static function createDate($value, string $rule): ?\DateTime
    {
        $date = \DateTime::createFromFormat(self::$format[$rule], $value);
        return $date ?: null;
    }

    private static function message(string $attribute, $value, string $rule, array $custom, $param = false): string
    {
        $default = [
            'after' => ':attribute deve ser maior ou igual a :param',
            'array' => ':attribute inválido',
            'before' => ':attribute deve ser menor ou igual a :param',
            'bool' => ':attribute dever ser true ou false',
            'cnpj' => ':attribute inválido',
            'cpf' => ':attribute inválido',
            'date' => ':attribute inválida',
            'datetime' => ':attribute inválida',
            'email' => ':attribute inválido',
            'float' => ':attribute inválido',
            'fullname' => ':attribute deve ser completo (sem abreviações)',
            'in' => ':attribute não encontrado',
            'int' => ':attribute não é um número inteiro',
            'length' => ':attribute possui tamanho inválido',
            'max' => ':attribute deve ser menor ou igual a :param',
            'min' => ':attribute deve ser maior ou igual a :param',
            'numeric' => ':attribute deve ser um número',
            'object' => ':attribute inválido',
            'query' => ':attribute inválido',
            'required' => ':attribute é obrigatório',
            'string' => ':attribute inválido',
            'time' => ':attribute inválida',
            'url' => ':attribute inválida',
        ];
        $search = $attribute . '.' . $rule;
        $error = @$custom[$search] ?: @$default[$rule] ?: $search;
        $error = str_replace(':attribute', (string) $attribute, $error);
        $error = str_replace(':value', (string) $value, $error);
        $error = str_replace(':param', (string) $param, $error);
        return $error;
    }

    private static function scheme(array $schemes): array
    {
        foreach ($schemes as $attribute => $scheme) {
            $rules = explode('|', (string) $scheme);
            $fix = [];
            foreach ($rules as $rule) {
                $param = explode(':', (string) $rule, 2);
                $fix[$param[0]] = @$param[1];
            }
            $schemes[$attribute] = $fix;
        }
        return $schemes;
    }
}
