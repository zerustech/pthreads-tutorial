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
 * A demo producer.
 *
 * Producer produces and puts products into the shared inventory.
 *
 * It descends from the {@link Collectable} class in order to tell the pool
 * whether it should be collected or not.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Producer extends \Collectable
{
    /**
     * @var string The name of producer.
     */
    private $name;

    /**
     * @var int The amount of products to be produced.
     */
    private $amount;

    /*
     * For demonstration purpose, we use string as the product type.
     * However, you can use anything type for it, as long as it has implemented
     * the ``__toString()`` method.
     *
     * @var string The product template.
     */
    private $product;

    /**
     * This property is used to slow down the thread to simulate variant
     * parallel circumstances.
     *
     * @var int The amount of delay, in seconds, after each product is produced.
     */
    private $delay;

    /**
     * Constructor.
     *
     * @param Threaded $inventory The shared inventory object.
     * @param int $amount The amount of products to be produced.
     * @param string $product The template / prototype for producing products.
     * @param int $delay The delay, in seconds.
     */
    public function __construct($name, $amount, $product = '*', $delay = 0)
    {
        $this->name = $name;

        $this->amount = $amount;

        $this->product = $product;

        $this->delay = $delay;
    }

    /**
     * Produces ``$amount`` products into the shared inventory.
     * @return void
     */
    public function run()
    {
        $remaining = $this->amount;

        while ($remaining > 0) {

            // Produces one product into the inventory.
            // Reuses the inventory in the context of current worker
            $this->worker->inventory->put($this->name, $this->product, $this->worker->name);

            // Slows down and allow consumer to consume more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }

        // Marks current thread as "garbage", so that it can be collected by the
        // collect() method of the pool.
        $this->setGarbage();
    }
}
