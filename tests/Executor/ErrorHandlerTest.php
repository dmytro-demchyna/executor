<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Tests;

use PDOException;
use SchemaKeeper\Tools\Executor\ErrorHandler;
use SchemaKeeper\Tools\Executor\Exception\RaisedException;

class ErrorHandlerTest extends ExecutorTestCase
{
    /**
     * @var ErrorHandler
     */
    private $target;

    function setUp()
    {
        parent::setUp();

        $this->target = new ErrorHandler();
    }

    function testSimple()
    {
        $sql = <<<'EOT'
DO $$
BEGIN 
RAISE EXCEPTION 'MyException';
END
$$
EOT;

        /** @var RaisedException $e */
        $e = $this->provokeError($sql);

        self::assertInstanceOf(RaisedException::class, $e);
        self::assertEquals('MyException', $e->getExceptionName());
        self::assertEquals('', $e->getExceptionHint());
        self::assertInstanceOf(PDOException::class, $e->getPrevious());
    }

    function testWithHint()
    {
        $sql = <<<'EOT'
DO $$
BEGIN 
    RAISE EXCEPTION 'MyException' 
    USING HINT = 'TestHint';
END
$$
EOT;

        /** @var RaisedException $e */
        $e = $this->provokeError($sql);

        self::assertInstanceOf(RaisedException::class, $e);
        self::assertEquals('MyException', $e->getExceptionName());
        self::assertEquals('TestHint', $e->getExceptionHint());
        self::assertInstanceOf(PDOException::class, $e->getPrevious());
    }

    function testOtherException()
    {
        $expected = new \Exception();

        $e = $this->target->handleError($expected);
        self::assertSame($expected, $e);
    }

    function testSyntaxError()
    {
        $sql = <<<'EOT'

EOT;

        $e = $this->provokeError($sql);

        self::assertInstanceOf(PDOException::class, $e);
        self::assertEquals('SQLSTATE[HY000]: General error: trying to execute an empty query', $e->getMessage());
        self::assertNull($e->getPrevious());
    }

    private function provokeError($sql)
    {
        $apiError = null;
        $conn = $this->getConn();

        try {
            $conn->exec($sql);
        } catch (\Exception $e) {
            $apiError = $this->target->handleError($e);
        }

        return $apiError;
    }
}
