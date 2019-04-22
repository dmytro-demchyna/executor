<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Exception;

class RaisedException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $exceptionName;

    /**
     * @var string
     */
    protected $exceptionHint;

    /**
     * @param string $exceptionName
     * @param string $exceptionHint
     * @param string $message
     * @param int $code
     * @param $previous
     */
    public function __construct($exceptionName, $exceptionHint, $message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->exceptionName = $exceptionName;
        $this->exceptionHint = $exceptionHint;
    }

    /**
     * @return string
     */
    public function getExceptionName()
    {
        return $this->exceptionName;
    }

    /**
     * @return string
     */
    public function getExceptionHint()
    {
        return $this->exceptionHint;
    }
}
