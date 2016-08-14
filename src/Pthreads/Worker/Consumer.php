<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ZerusTech\Tutorial\Pthreads\Worker;

use ZerusTech\Tutorial\Pthreads\Basic\Consumer as BaseConsumer;

/**
 * The consumer class that supports pthreads worker.
 *
 * Consumer takes products out of the shared inventory and consumes them.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Consumer extends BaseConsumer
{
    /**
     * Constructor.
     *
     * @param string $name The name of current consumer.
     * @param int $amount The amount of products to be consumed.
     * @param int $delay The delay, in seconds.
     */
    public function __construct($name, $amount, $delay = 0)
    {
        $this->name = $name;

        $this->amount = $amount;

        $this->delay = $delay;
    }

    /**
     * Consumes ``$amount`` products from the shared inventory.
     * @return void
     */
    public function run()
    {
        $remaining = $this->amount;

        while ($remaining > 0) {

            // Consumes one product from the inventory.
            $product = $this->worker->inventory->get($this);

            // Slows down and allow producer to produce more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}
