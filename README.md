# Executor

[![Minimum PHP Version](https://img.shields.io/badge/PHP-%3E%3D%205.6-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum PostgreSQL Version](https://img.shields.io/badge/PostgreSQL-%3E%3D%209.4-8892BF.svg?style=flat-square)](https://www.postgresql.org/)
[![Build Status](https://travis-ci.com/dmytro-demchyna/executor.svg?branch=master)](https://travis-ci.com/dmytro-demchyna/executor)

Executor is the PHP-library that helps to execute [PL/pgSQL](https://www.postgresql.org/docs/current/plpgsql.html) stored procedures.

## Examples

### Exception handling

Suppose the stored procedure that throws an exception:

```postgresql
CREATE OR REPLACE FUNCTION public.test_function() 
  RETURNS VOID
  LANGUAGE plpgsql
AS $function$
BEGIN
   RAISE EXCEPTION 'MyException' USING HINT = 'TestHint';
END;
$function$;
```

Call it via **Executor**:

```php
<?php

use SchemaKeeper\Tools\Executor\Fetcher\SingleColumn;
use SchemaKeeper\Tools\Executor\Exception\RaisedException;

try {
    $executor->execFunc('public.test_function', [], new SingleColumn()); 
} catch (RaisedException $e) {
    echo $e->getExceptionName().' '.$e->getExceptionHint();
}
```

Echo in the `catch` block will output: "MyException TestHint"  

### Fetching

Suppose the stored procedure that returns table:

```postgresql
CREATE OR REPLACE FUNCTION public.test_function(_dummy TEXT)
    RETURNS TABLE(param1 INTEGER, param2 TEXT)
    LANGUAGE plpgsql
AS $function$
BEGIN
    param1 = 1;
    param2 = 'One';
    RETURN NEXT;

    param1 = 2;
    param2 = 'Two';
    RETURN NEXT;

    RETURN;
END;
$function$;
```

**Executor** provides three different fetchers:

```php
<?php

use SchemaKeeper\Tools\Executor\Fetcher\SingleColumn;
use SchemaKeeper\Tools\Executor\Fetcher\SingleRow;
use SchemaKeeper\Tools\Executor\Fetcher\MultipleRow;

$params = [':dummy' => 'test'];

$result1 = $executor->execFunc('public.test_function', $params, new SingleColumn());
$result2 = $executor->execFunc('public.test_function', $params, new SingleRow());
$result3 = $executor->execFunc('public.test_function', $params, new MultipleRow());

var_dump($result1, $result2, $result3);
```

`var_dump` will output:

```
int(1)

array(2) {
  'param1' =>
  int(1)
  'param2' =>
  string(3) "One"
}

array(2) {
  [0] =>
  array(2) {
    'param1' =>
    int(1)
    'param2' =>
    string(3) "One"
  }
  [1] =>
  array(2) {
    'param1' =>
    int(2)
    'param2' =>
    string(3) "Two"
  }
}
```

### Initialization

```php
<?php

use SchemaKeeper\Tools\Executor\Connection\PDOProxy;
use SchemaKeeper\Tools\Executor\ErrorHandler;
use SchemaKeeper\Tools\Executor\Executor;

$dsn = 'pgsql:dbname=schema_keeper;host=localhost';

$pdo = new PDO($dsn, 'postgres', 'postgres', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$conn = new PDOProxy($pdo);

$errorHandler = new ErrorHandler();

$executor = new Executor($conn, $errorHandler);
```

## Contributing
Please refer to [CONTRIBUTING.md](https://github.com/dmytro-demchyna/executor/blob/master/.github/CONTRIBUTING.md) for information on how to contribute to SchemaKeeper.