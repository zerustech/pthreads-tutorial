<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Issue603;

/**
 * The inventory class to demonstrate issue {@link https://github.com/krakjoe/pthreads/issues/603 603}.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Inventory extends \Threaded
{
    /**
     * @var string The internal queue.
     */
    private $queue;

    /**
     * Constructor.
     *
     * @param \Threaded $queue The internal queue. 
     */
    public function __construct(\Threaded $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Puts a product into the internal queue.
     *
     * @param \Thread $producer Current producer.
     * @param string $produce The product to be added.
     * @return void
     */
    public function put(\Thread $producer, $product)
    {
        $this->queue[] = $product;

        printf("[%s] instance [%s] in worker [%s] is producing now ...\n\n", get_class($producer), $producer->name, $producer->worker->name);
    }

}

/**
 * A producer class to demonstrate issue 603.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class ProducerFoo extends \Thread
{
    /**
     * @var string The producer name.
     */
    private $name;

    /**
     * Constructor.
     * @param string $name The producer name.
     *
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Puts a product into the inventory.
     * @return void
     */
    public function run()
    {
        printf("[ProducerFoo] [%s] in worker [%s] is about to run ...\n", $this->name, $this->worker->name);
        $this->worker->inventory->put($this, '*');
    }
}

/**
 * An other producer to demonstrate issue 603.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class ProducerBar extends \Thread
{
    /**
     * @var string The producer name.
     */
    private $name;

    /**
     * Constructor.
     *
     * @param string $name The producer name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Adds a produce into the inventory.
     * @return void
     */
    public function run()
    {
        printf("[ProducerBar] [%s] in worker [%s] is about to run ...\n", $this->name, $this->worker->name);
        $this->worker->inventory->put($this, '*');
    }
}

// Constructs the inventory and queue.
$queue = new \Threaded();
$inventory = new Inventory($queue);

// Initializes three producer instances:
// Two ProducerFoo instances and one ProducerBar instance.
$p1 = new ProducerFoo('p1');
$p2 = new ProducerFoo('p2');
$p3 = new ProducerBar('p3');

// Initializes a worker.
$worker = new \Worker();
$worker->inventory = $inventory;
$worker->name = "w1";

// Stacks producers to the worker
$worker->stack($p1);
$worker->stack($p2);
$worker->stack($p3);

// Starts the worker, the threads will be started.
$worker->start();

$worker->shutdown();


// Expected output:
//
// [ProducerFoo] [p1] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerFoo] instance [p1] in worker 
// [w1] is producing now ...
//
// [ProducerFoo] [p2] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerFoo] instance [p2] in worker 
// [w1] is producing now ...
//
// [ProducerBar] [p3] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerBar] instance [p3] in worker 
// [w1] is producing now ...
//
//
// Actual output:
//
// [ProducerFoo] [p1] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerFoo] instance [p1] in worker 
// [w1] is producing now ...
//
// [ProducerFoo] [p2] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerFoo] instance [p1] in worker 
// [w1] is producing now ...
//
// [ProducerBar] [p3] in worker [w1] is about to run ...
// [ZerusTech\Tutorial\Pthreads\Issue603\ProducerFoo] instance [p1] in worker 
// [w1] is producing now ...
//
