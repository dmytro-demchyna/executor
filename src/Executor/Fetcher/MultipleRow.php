<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Tools\Executor\Fetcher;

class MultipleRow extends Fetcher
{
    /**
     * @param \PDOStatement $stmt
     * @return array|bool
     */
    public function fetch(\PDOStatement $stmt)
    {
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $rows;
    }
}
