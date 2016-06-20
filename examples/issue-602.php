<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Issue602;

/**
 * The producer class to demonstrate issue {@link https://github.com/krakjoe/pthreads/issues/602 602}.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Producer extends \Thread
{
    /**
     * @var Inventory The inventory.
     */
    private $inventory;

    /**
     * Constructor.
     *
     * @param Inventory $inventory The product inventory.
     */
    public function __construct($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * Adds a product into the inventory and reports the inventory status.
     */
    public function run()
    {
        $this->inventory->add('*');
        $this->inventory->report();
    }
}

/**
 * The inventory class to demonstrate issue 602.
 */
class Inventory extends \Threaded
{
    /**
     * @var \Threaded The internal queue.
     */
    private $queue;

    /**
     * Constructor.
     *
     * @var \Threaded $queue The internal queue.
     */
    public function __construct(\Threaded $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Adds a product into the inventory.
     * @param string $product The product.
     * @return void
     */
    public function add($product)
    {
        $this->queue[] = $product;
    }

    /**
     * Reports the inventory status.
     * @return string The inventory status.
     */
    public function report()
    {
        $msg = '';

        foreach ($this->queue as $product) {
            $msg .= $product;
        }

        echo $msg."\n";
    }
}

// This works as expected
/*
$queue = new Threaded();
$inventory = new Inventory($queue);
$p = new Producer($inventory);
$p->start();
*/

// This does not work
// Refer to issue #602 (https://github.com/krakjoe/pthreads/issues/602) for details.
$queue = new \Threaded();
$inventory = new Inventory($queue);
$pool = [];
$pool[] = new Producer($inventory);
$pool[0]->start();
// It works, if the following line is uncommented:
// $pool[0]->join();
