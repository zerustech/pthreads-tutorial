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

use ZerusTech\Tutorial\Pthreads\Worker\Producer as WorkerProducer;

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
class Producer extends WorkerProducer implements \Collectable
{
    /**
     * @var bool The boolean to indicates if current thread can be collected.
     */
    private $garbage;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $amount, $product = '*', $delay = 0)
    {
        parent::__construct($name, $amount, $product, $delay);

        $this->garbage = false;
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
            $this->worker->inventory->put($this, $this->product);

            // Slows down and allow consumer to consume more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }

        $this->garbage = true;
    }

    /**
     * This method returns a boolean that indicates whether current thread can
     * be collected.
     *
     * @return bool True if current thread can be collected, false otherwise.
     */
    public function isGarbage(): bool {

        return $this->garbage;
    }
}
