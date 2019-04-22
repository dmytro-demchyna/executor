<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Tests;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class ExecutorTestCase extends TestCase
{
    /**
     * @var \PDO
     */
    private static $conn;

    protected function tearDown()
    {
        parent::tearDown();

        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );

        \Mockery::close();
    }

    /**
     * @return PDO
     */
    protected static function getConn()
    {
        if (!self::$conn) {
            self::$conn = self::createConn();
        }

        return self::$conn;
    }

    /**
     * @return PDO
     */
    private static function createConn()
    {
        $dsn = 'pgsql:dbname=schema_keeper;host=postgres';

        $conn = new \PDO($dsn, 'postgres', 'postgres', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        return $conn;
    }
}
