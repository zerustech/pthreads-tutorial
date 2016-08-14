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

use ZerusTech\Tutorial\Pthreads\Basic\Producer as BaseProducer;

/**
 * A producer class that supports pthreads worker.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Producer extends BaseProducer
{
    /**
     * Constructor.
     *
     * @param string $name The name of current producer.
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
            $this->worker->inventory->put($this->name, $this->product, $this->worker->name);

            // Slows down and allow consumer to consume more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}
