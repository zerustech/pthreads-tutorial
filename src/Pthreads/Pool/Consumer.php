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

use ZerusTech\Tutorial\Pthreads\Worker\Consumer as WorkerConsumer;

/**
 * A demo consumer.
 *
 * Consumer takes products out of the shared inventory and consumes them.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Consumer extends WorkerConsumer implements \Collectable
{
    /**
     * @var bool The boolean to indicates if current thread can be collected.
     */
    private $garbage;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $amount, $delay = 0)
    {
        parent::__construct($name, $amount, $delay);

        $this->garbage = true;
    }

    /**
     * Consumes ``$amount`` products from the shared inventory.
     * @return void
     */
    public function run()
    {
        $remaining = $this->amount;

        while ($remaining > 0) {

            $product = $this->worker->inventory->get($this);

            // Slows down and allow producer to produce more products.
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
