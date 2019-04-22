<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor;

use SchemaKeeper\Tools\Executor\Connection\IConnection;
use SchemaKeeper\Tools\Executor\Fetcher\Fetcher;

class Executor
{
    /**
     * @var IConnection
     */
    protected $conn;

    /**
     * @var ErrorHandler
     */
    protected $errorHandler;

    public function __construct(IConnection $conn, ErrorHandler $errorHandler)
    {
        $this->conn = $conn;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->conn->commit();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        return $this->conn->rollBack();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->conn->inTransaction();
    }

    /**
     * @param string $functionName
     * @param array $paramValues
     * @param Fetcher $fetcher
     * @return mixed
     * @throws \Exception
     */
    public function execFunc($functionName, array $paramValues, Fetcher $fetcher)
    {
        $params = implode(',', array_keys($paramValues));

        array_walk($paramValues, function (&$item) {
            $item = is_bool($item) ? (string)(int)$item : $item;
        });

        $sql = "SELECT f.* FROM  $functionName($params) f";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute($paramValues);

            $result = $fetcher->fetch($stmt);
        } catch (\Exception $e) {
            $exception = $this->errorHandler->handleError($e);

            throw $exception;
        }

        return $result;
    }
}
