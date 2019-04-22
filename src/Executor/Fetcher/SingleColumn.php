<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Fetcher;

class SingleColumn extends Fetcher
{
    /**
     * @param \PDOStatement $stmt
     * @return mixed
     */
    public function fetch(\PDOStatement $stmt)
    {
        $column = $stmt->fetchColumn();
        
        return $column;
    }
}
