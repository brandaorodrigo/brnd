<?php

class Response
{
    public static function json(?array $array = null, int $code = 200): void
    {
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        http_response_code($code);
        if (!$array) {
            echo '{}';
            exit();
        }
        echo html_entity_decode(json_encode($array)) ?: '{}';
        exit();
    }

    public static function ifNotFound(string $table, string $column, string $id): void
    {
        $exists = DB::exists($table, $column, $id);
        if (!$exists) {
            self::json(['message' => ['Id not found']], 404);
        }
    }

    public static function ifInvalid(array $data, array $validations, ?array $custom = []): void
    {
        $message = Validate::execute($data, $validations, $custom);
        if ($message) {
            self::json(['message' => $message], 400);
        }
    }

    public static function control(string $query, ?array $params = null): void
    {
        $request = Request::all();
        // ---------------------------------------------------------------------
        $message = Validate::execute($request, [
            'limit' => 'required|numeric|min:1',
            'page' => 'required|numeric|min:1',
            'sort' => 'required|string',
            'order' => 'required|in:asc,desc',
        ]);
        if ($message) {
            self::json(['message' => $message], 400);
        }
        // ---------------------------------------------------------------------
        $values = Normalize::execute($request, [
            'limit' => 'int',
            'page' => 'int',
            'sort' => 'string',
            'order' => 'string',
        ]);
        // ---------------------------------------------------------------------
        foreach ($values as $key => $value) {
            ${$key} = $value;
        }
        $offset = ($page - 1) * $limit;
        $query_control = $query . " ORDER BY {$sort} {$order} OFFSET {$offset} ROWS FETCH NEXT {$limit} ROWS ONLY";
        $rows = DB::select($query_control, $params);
        $count = DB::count($query, $params);
        // ---------------------------------------------------------------------
        self::json(['rows' => $rows, 'count' => $count]);
    }
}
