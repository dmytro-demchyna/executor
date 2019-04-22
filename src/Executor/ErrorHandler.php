<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor;

use PDOException;
use SchemaKeeper\Tools\Executor\Exception\RaisedException;

class ErrorHandler
{
    /**
     * @param \Exception $e
     * @return \Exception
     */
    public function handleError(\Exception $e)
    {
        if (!($e instanceof PDOException)) {
            return $e;
        }

        if (count($e->errorInfo) != 3) {
            return $e;
        }

        $rawText = $e->errorInfo[2];

        $parts = explode("\n", $rawText);
        $exceptionName = trim(str_replace('ERROR:', '', $parts[0]));
        $rawSurplus = isset($parts[1]) ? $parts[1] : '';
        $exceptionHint = '';

        if (stripos($rawSurplus, 'HINT:') !== false) {
            $exceptionHint = trim(str_replace('HINT:', '', $rawSurplus));
        }

        $executorException = new RaisedException($exceptionName, $exceptionHint, $e->getMessage(), 0, $e);

        return $executorException;
    }
}
