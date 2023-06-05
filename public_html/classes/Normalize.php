<?php

class Normalize
{

    // filters / type ----------------------------------------------------------

    public static function bool(string $value): bool
    {
        return boolval($value);
    }

    public static function float(string $value): float
    {
        return floatval($value);
    }

    public static function int(string $value): int
    {
        return intval($value);
    }

    public static function none(string $value): string
    {
        return $value;
    }

    public static function string(string $value): string
    {
        return $value;
    }

    // filters / format --------------------------------------------------------

    public static function currency(string $value, string $param): string
    {
        $numbers = @$param[0] ?: 2;
        $decimals = @$param[1] ?: ',';
        $thousands = @$param[2] ?: '.';
        return number_format(floatval($value), $numbers, $decimals, $thousands);
    }

    public static function number(string $value, string $param): string
    {
        $numbers = @$param[0] ?: 0;
        $decimals = @$param[1] ?: ',';
        $thousands = @$param[2] ?: '.';
        return number_format(floatval($value), $numbers, $decimals, $thousands);
    }

    public static function date(string $value, ?string $param = DATE_ATOM): string
    {
        if (strlen(preg_replace('/[^0-9]/i', '', $value)) != 10) {
            $value = strtotime($value);
        }
        if (!$param) {
            throw new \Exception('Required parameter.');
        }
        return date($param, $value);
    }

    public static function dateConvert(string $value, string $param): string
    {
        $param = explode(',', $param, 2);
        if (count($param) != 2) {
            throw new \Exception('Incorrect number of parameters.');
        }
        $date = \DateTime::createFromFormat('!' . $param[0], $value);
        if ($date) {
            $value = $date->format($param[1]);
        }
        return $value;
    }

    // filters / replace -------------------------------------------------------

    public static function alpha(string $value): string
    {
        return preg_replace('/[^A-Za-z ]/i', '', $value);
    }

    public static function alphanumeric(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9 ]/i', '', $value);
    }

    public static function numeric(string $value): string
    {
        return preg_replace('/[^0-9 ]/i', '', $value);
    }

    public static function trim(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    public static function charset(string $value, string $param): string
    {
        $param = explode(',', $param, 2);
        if (count($param) != 2) {
            throw new \Exception('Incorrect number of parameters.');
        }
        $value = mb_convert_encoding($value, $param[0], $param[1]);
        return $value;
    }

    // filters / mask ----------------------------------------------------------

    public static function mask(string $value, string $param): string
    {
        if (!$param) {
            throw new \Exception('Required parameter.');
        }
        $masked = '';
        if ($value) {
            $k = 0;
            for ($i = 0; $i <= strlen($param) - 1; $i++) {
                if ($param[$i] == '#') {
                    if (isset($value[$k])) {
                        $masked .= $value[$k++];
                    }
                } else {
                    if (isset($param[$i])) {
                        $masked .= $param[$i];
                    }
                }
            }
        }
        return $masked;
    }

    public static function cnpj(string $value): string
    {
        return self::mask($value, '##.###.###/####-##');
    }

    public static function cpf(string $value): string
    {
        return self::mask($value, '###.###.###-##');
    }

    public static function zipcode(string $value): string
    {
        return self::mask($value, '#####-###');
    }

    // filters / case ----------------------------------------------------------

    public static function lower(string $value): string
    {
        return mb_convert_case($value, MB_CASE_LOWER, 'UTF-8');
    }

    public static function title(string $value): string
    {
        $value = mb_strtolower($value);
        $explode = explode(' ', $value);
        $return = '';
        foreach ($explode as $value) {
            $return .= ucfirst($value) . ' ';
        }
        return trim($return);
    }

    public static function upper(string $value): string
    {
        return mb_convert_case($value, MB_CASE_UPPER, 'UTF-8');
    }

    public static function sentence(string $value): string
    {
        return ucfirst(mb_strtolower($value));
    }

    // filters / add -----------------------------------------------------------

    public static function prefix(string $value, string $param): string
    {
        return $param . $value;
    }

    public static function sufix(string $value, string $param): string
    {
        return $value . $param;
    }

    public static function zeros(string $value, string $param): string
    {
        return str_pad($value, $param, '0', STR_PAD_LEFT);
    }

    // filters / encrypt -------------------------------------------------------

    public static function decode(string $value, string $param): ?string
    {
        if (!$param) {
            throw new \Exception('Required parameter.');
        }
        $cipher = 'aes-128-cbc';
        $ivlen = openssl_cipher_iv_length($cipher);
        $value = str_replace(['4rfvv', '5tgbb', '6yhnn'], ['+', '/', '='], $value);
        $c = base64_decode($value);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $return = openssl_decrypt($ciphertext_raw, $cipher, $param, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $param, $as_binary = true);
        if (!hash_equals($hmac, $calcmac)) {
            return null;
        }
        return $return;
    }

    public static function encode(string $value, string $param): string
    {
        if (!$param) {
            throw new \Exception('Required parameter.');
        }
        $cipher = 'aes-128-cbc';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $raw = openssl_encrypt($value, $cipher, $param, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $raw, $param, $as_binary = true);
        $return = base64_encode($iv . $hmac . $raw);
        $return = str_replace(['+', '/', '='], ['4rfvv', '5tgbb', '6yhnn'], $return);
        return $return;
    }

    // execute -----------------------------------------------------------------

    private static function single(array $data, array $filters): array
    {
        $scheme = self::scheme($filters);
        $return = [];
        foreach ($scheme as $attribute => $rules) {
            $value = @$data[$attribute];
            foreach ($rules as $rule => $param) {
                if ($value !== false && trim((string) $value) === '') {
                    $value = null;
                    break;
                }
                if (method_exists(__CLASS__, $rule)) {
                    $value = self::$rule((string) $value, (string) $param);
                    continue;
                }
                throw new \Exception($rule . ' does not exists.');
            }
            $return[$attribute] = $value;
        }
        return $return;
    }

    public static function execute(array $data, array $filters): array
    {
        if (is_array(reset($data))) {
            $return = [];
            foreach ($data as $current) {
                if ($current) {
                    $return[] = self::single($current, $filters);
                }
            }
            return $return;
        }
        return self::single($data, $filters);
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

    // utils -------------------------------------------------------------------

    public static function implode(string $glue, array $array): string
    {
        $return = '';
        foreach ($array as $value) {
            $return .= $value . $glue;
        }
        $return = substr($return, 0, strlen($glue) * -1);
        return $return;
    }

    public static function filter(array $array, array $filter)
    {
        return array_intersect_key($array, array_flip($filter));
    }

    public static function imageBase64(string $url): ?string
    {
        $type = pathinfo($url, PATHINFO_EXTENSION);
        @$file = file_get_contents($url);
        if (!$file) {
            return null;
        }
        return 'data:image/' . $type . ';charset=utf-8;base64,' . base64_encode($file);
    }
}
