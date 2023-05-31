<?php

class DB
{
    private static $pdo;

    private static function exception(string $message): never
    {
        throw new \Exception($message);
    }

    private static function connect(): \PDO
    {
        if (!static::$pdo) {
            global $_DB;
            try {
                $dsn = 'sqlsrv:Server=' . $_DB['host'] . ($_DB['port'] ? ',' . $_DB['port'] : '') . ';Database=' . $_DB['base'];
                static::$pdo = new \PDO($dsn, $_DB['user'], $_DB['pass']);
            } catch (\PDOException $exception) {
                static::exception($exception->getMessage());
            }
            if (@$_DB['debug']) {
                $mode = \PDO::ATTR_ERRMODE;
                $exception = \PDO::ERRMODE_EXCEPTION;
                static::$pdo->setAttribute($mode, $exception);
            }
            static::$pdo->setAttribute(\PDO::SQLSRV_ATTR_DIRECT_QUERY, true);
            $sql = 'SET DATEFORMAT ymd';
            static::query($sql);
        }
        return static::$pdo;
    }

    public static function query(string $query, ?array $params = null): ?\PDOStatement
    {
        $pdo = static::connect();
        try {
            $statement = $pdo->prepare($query);
        } catch (\PDOException $exception) {
            static::exception($exception->getMessage());
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
            static::exception($exception->getMessage());
        }
        return $statement ?: null;
    }

    public static function select(string $query, ?array $params = null): array
    {
        $fetch = \PDO::FETCH_ASSOC; // FETCH_OBJ
        $return = [];
        $statement = static::query($query, $params);
        if ($statement->columnCount() > 0) {
            while ($row = @$statement->fetch($fetch)) {
                $return[] = $row;
            }
        }
        return $return;
    }

    public static function first(string $query, ?array $params = null): array
    {
        $select = static::select($query, $params);
        if (count($select)) {
            return reset($select);
        }
        return [];
    }

    public static function value(string $query, ?array $params = null): ?string
    {
        $value = static::first($query, $params);
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
        static::query($sql, $params);
    }

    public static function update(string $table, array $array, string $column, int $id): void
    {
        $set = $params = [];
        foreach ($array as $key => $value) {
            $set[] = $key . ' = ?';
            $params[] = $value;
        }
        $params[] = $id;
        $set = self::implode(', ', $set);
        $sql = "UPDATE {$table} SET {$set} WHERE {$column} = ?";
        static::query($sql, $params);
    }

    public static function delete(string $table, string $column, int $id): void
    {
        $sql = "DELETE FROM {$table} WHERE {$column} = ?";
        static::query($sql, [$id]);
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
        return static::select($sql, $params);
    }

    public static function lastInsertId(): ?int
    {
        $id = static::$pdo->lastInsertId();
        return $id ? (int) $id : null;
    }

    public static function disconnect(): never
    {
        static::$pdo = null;
    }

    // utils -------------------------------------------------------------------

    private static function implode(string $glue, array $array): string
    {
        $return = '';
        foreach ($array as $a) {
            $return .= $a . $glue;
        }
        $return = substr($return, 0, strlen($glue) * -1);
        return $return;
    }
}
