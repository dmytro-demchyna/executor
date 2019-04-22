<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Connection;

use PDO;

class PDOProxy implements IConnection
{
    /**
     * @var PDO
     */
    private $conn;

    /**
     * @param PDO $conn
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function inTransaction()
    {
        return $this->conn->inTransaction();
    }

    public function prepare($statement, array $driver_options = array())
    {
        return $this->conn->prepare($statement, $driver_options);
    }

    public function rollBack()
    {
        return $this->conn->rollBack();
    }
}
