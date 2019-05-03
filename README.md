# Executor

Executor is the PHP-library that helps to execute [PL/pgSQL](https://www.postgresql.org/docs/current/plpgsql.html) stored procedures.

## Examples

### Exception handling

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

```php
<?php

use SchemaKeeper\Tools\Executor\Fetcher\SingleColumn;
use SchemaKeeper\Tools\Executor\Exception\RaisedException;

try {
    
    $executor->execFunc('public.test_function', [], new SingleColumn());
    
} catch (RaisedException $e) {
    
    echo $e->getExceptionName().' '.$e->getExceptionHint();
  
    // Will display: "MyException TestHint"  
}
```

### Fetching

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

```php
<?php

use SchemaKeeper\Tools\Executor\Fetcher\MultipleRow;

$result = $executor->execFunc('public.test_function', [':dummy' => 'test'], new MultipleRow());

var_dump($result);

/*
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
 */

```

### Initialization

```php
<?php

$dsn = 'pgsql:dbname=schema_keeper;host=localhost';

$pdo = new \PDO($dsn, 'postgres', 'postgres', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
]);

$conn = new \SchemaKeeper\Tools\Executor\Connection\PDOProxy($pdo);

$errorHandler = new \SchemaKeeper\Tools\Executor\ErrorHandler();

$executor = new \SchemaKeeper\Tools\Executor\Executor($conn, $errorHandler);
```

## Contributing
Please refer to [CONTRIBUTING.md](https://github.com/dmytro-demchyna/executor/blob/master/.github/CONTRIBUTING.md) for information on how to contribute to SchemaKeeper.