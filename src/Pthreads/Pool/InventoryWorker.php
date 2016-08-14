<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Pool;

/**
 * A demo worker class.
 */
class InventoryWorker extends \Worker
{
    /**
     * @var string The worker name.
     */
    private $name;

    /**
     * @var string The inventory.
     */
    private $inventory;

    /**
     * Constructor.
     *
     * @param string $inventory The inventory.
     */
    public function __construct($prefix, $inventory)
    {
        $this->inventory = $inventory;

        $this->name = uniqid($prefix);
    }
}
