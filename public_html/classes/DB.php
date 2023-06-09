<?php

class DB
{

    public static $pdo;

    public static function config()
    {
        global $_DB;
        return $_DB;
    }

    public static function connect()
    {
        if (!static::$pdo) {
            try {
                $db = static::config();
                $dsn = 'sqlsrv:Server=' . $db['server'] . ';Database=' . $db['database'];
                static::$pdo = new \PDO($dsn, $db['username'], $db['password']);
            } catch (\PDOException $exception) {
                self::exception($exception->getMessage());
                return;
            }
            if (@$db['debug']) {
                $mode = \PDO::ATTR_ERRMODE;
                $exception = \PDO::ERRMODE_EXCEPTION;
                static::$pdo->setAttribute($mode, $exception);
            }
            static::$pdo->setAttribute(\PDO::SQLSRV_ATTR_DIRECT_QUERY, true);
            $sql = 'SET DATEFORMAT ymd';
            self::query($sql);
        }
        return static::$pdo;
    }

    public static function query(string $query, ?array $params = null): ?\PDOStatement
    {
        $pdo = self::connect();
        try {
            $statement = $pdo->prepare($query, [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL]);
        } catch (\PDOException $exception) {
            self::exception($exception->getMessage());
        }
        if ($params) {
            $position = 0;
            foreach ($params as $value) {
                if (is_bool($value)) {
                    $type = \PDO::PARAM_BOOL;
                } elseif (is_int($value)) {
                    $type = \PDO::PARAM_INT;
                } elseif (is_null($value)) {
                    $type = \PDO::PARAM_NULL;
                } elseif (is_float($value)) {
                    $type = \PDO::PARAM_STR;
                } else {
                    $type = \PDO::PARAM_STR;
                }
                $position++;
                $statement->bindValue($position, $value, $type);
            }
        }
        try {
            $statement->execute();
        } catch (\PDOException $exception) {
            self::exception($exception->getMessage());
        }
        return $statement ?: null;
    }

    public static function count(string $query, ?array $params = null): int
    {
        $statement = self::query($query, $params);
        return (int)$statement->rowCount();
    }

    public static function select(string $query, ?array $params = null): array
    {
        $fetch = \PDO::FETCH_ASSOC; // FETCH_OBJ
        $return = [];
        $statement = self::query($query, $params);
        if ($statement->columnCount() > 0) {
            while ($row = @$statement->fetch($fetch)) {
                $return[] = array_change_key_case($row, CASE_LOWER);
            }
        }
        return $return;
    }

    public static function first(string $query, ?array $params = null): array
    {
        $select = self::select($query, $params);
        if (count($select)) {
            return reset($select);
        }
        return [];
    }

    public static function value(string $query, ?array $params = null): ?string
    {
        $value = self::first($query, $params);
        if ($value) {
            $value = (array) $value;
            return reset($value);
        }
        return null;
    }

    public static function insert(string $table, array $array): void
    {
        $fields = $params = $qms = [];
        foreach ($array as $key => $value) {
            $fields[] = $key;
            $params[] = $value;
            $qms[] = '?';
        }
        $fields = self::implode(', ', $fields);
        $qms = self::implode(', ', $qms);
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$qms})";
        self::query($sql, $params);
    }

    public static function update(string $table, array $array, string $column, string $id): void
    {
        $set = $params = [];
        foreach ($array as $key => $value) {
            $set[] = $key . ' = ?';
            $params[] = $value;
        }
        $params[] = $id;
        $set = self::implode(', ', $set);
        $sql = "UPDATE {$table} SET {$set} WHERE {$column} = ?";
        self::query($sql, $params);
    }

    public static function delete(string $table, string $column, string $id): void
    {
        $sql = "DELETE FROM {$table} WHERE {$column} = ?";
        self::query($sql, [$id]);
    }

    public static function exists(string $table, string $column, string $id): bool
    {
        return DB::value("SELECT TOP 1 1 FROM {$table} WHERE {$column} = ?", [$id]) ? true : false;
    }

    public static function procedure(string $procedure, array $array): array
    {
        $set = $params = [];
        foreach ($array as $key => $value) {
            $set[] = "@{$key} = ?";
            $params[] = $value;
        }
        $set = self::implode(', ', $set);
        $sql = "SET NOCOUNT ON; EXECUTE {$procedure} {$set};";
        return self::select($sql, $params);
    }

    public static function lastInsertId(): ?int
    {
        $id = static::$pdo->lastInsertId();
        return $id ? (int) $id : null;
    }

    public static function disconnect(): void
    {
        static::$pdo = null;
    }

    private static function exception(string $message): void
    {
        $db = static::config();
        if (@$db['debug']) {
            throw new \Exception($message);
            exit();
        }
    }

    // utils -------------------------------------------------------------------

    private static function implode(string $glue, array $array): string
    {
        $return = '';
        foreach ($array as $value) {
            $return .= $value . $glue;
        }
        $return = substr($return, 0, strlen($glue) * -1);
        return $return;
    }
}
