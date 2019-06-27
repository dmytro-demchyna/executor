<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Tests;

use Mockery\MockInterface;
use SchemaKeeper\Tools\Executor\Connection\IConnection;
use SchemaKeeper\Tools\Executor\Connection\PDOProxy;
use SchemaKeeper\Tools\Executor\ErrorHandler;
use SchemaKeeper\Tools\Executor\Executor;
use SchemaKeeper\Tools\Executor\Fetcher\SingleColumn;

class ExecutorTest extends ExecutorTestCase
{
    /**
     * @var ErrorHandler|MockInterface
     */
    private $errorHandler;

    /**
     * @var Executor
     */
    private $target;

    function setUp()
    {
        parent::setUp();

        $conn = new PDOProxy($this->getConn());

        $this->errorHandler = \Mockery::mock(ErrorHandler::class);
        $this->target = new Executor($conn, $this->errorHandler);
    }

    function testItExecuteFunction()
    {
        $this->errorHandler->shouldNotReceive('handleError');

        $result = $this->target->execFunc('public.test_function', [':param' => 4], new SingleColumn());
        self::assertSame(8, $result);
    }

    function testItExecuteAllowedFunction()
    {
        $this->target->setAllowedFunctions(['public.test_function']);
        $this->errorHandler->shouldNotReceive('handleError');

        $result = $this->target->execFunc('public.test_function', [':param' => 4], new SingleColumn());
        self::assertSame(8, $result);
    }

    /**
     * @expectedException \SchemaKeeper\Tools\Executor\Exception\ForbiddenFunction
     * @expectedExceptionMessage Execution of the public.test_function is not allowed
     */
    function testItNotExecuteForbiddenFunction()
    {
        $this->target->setAllowedFunctions([]);
        $this->errorHandler->shouldNotReceive('handleError');

        $this->target->execFunc('public.test_function', [':param' => 4], new SingleColumn());
    }

    /**
     * @expectedException \PDOException
     * @expectedExceptionMessage invalid input syntax for integer: "qwerty"
     */
    function testItHandleError()
    {
        $this->errorHandler->shouldReceive('handleError')->with(\Mockery::type(\PDOException::class))
            ->andReturnUsing(function ($exception) {
                return $exception;
            })->once();

        $this->target->execFunc('test_function', [':test' => 'qwerty'], new SingleColumn());
    }

    function testItReplaceBoolean()
    {
        $this->errorHandler->shouldNotReceive('handleError');

        $result = $this->target->execFunc('test_function2', [
            ':test1' => true,
        ], new SingleColumn());

        $this->assertSame(false, $result);
    }

    function testItBeginTransaction()
    {
        $conn = \Mockery::mock(IConnection::class);
        $this->target = new Executor($conn, $this->errorHandler);

        $conn->shouldReceive('beginTransaction')->andReturn(true)->once();

        $result = $this->target->beginTransaction();
        self::assertTrue($result);
    }

    function testItCommit()
    {
        $conn = \Mockery::mock(IConnection::class);
        $this->target = new Executor($conn, $this->errorHandler);

        $conn->shouldReceive('commit')->andReturn(true)->once();

        $result = $this->target->commit();
        self::assertTrue($result);
    }

    function testItRollback()
    {
        $conn = \Mockery::mock(IConnection::class);
        $this->target = new Executor($conn, $this->errorHandler);

        $conn->shouldReceive('rollBack')->andReturn(true)->once();

        $result = $this->target->rollBack();
        self::assertTrue($result);
    }

    function testItInTransaction()
    {
        $conn = \Mockery::mock(IConnection::class);
        $this->target = new Executor($conn, $this->errorHandler);

        $conn->shouldReceive('inTransaction')->andReturn(true)->once();

        $result = $this->target->inTransaction();
        self::assertTrue($result);
    }
}
