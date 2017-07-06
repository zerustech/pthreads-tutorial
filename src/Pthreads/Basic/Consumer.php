<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Basic;

/**
 * A demo consumer.
 *
 * Consumer takes products out of the shared inventory and consumes them.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Consumer extends \Thread
{
    /**
     * @var Inventory The shared inventory.
     */
    protected $inventory;

    /**
     * @var string The name of current consumer.
     */
    protected $name;

    /**
     * @var int The amount of products to be consumed.
     */
    protected $amount;

    /**
     * This property is used to slow down the thread to simulate variant
     * parallel circumstances.
     *
     * @var int The amount of delay, in seconds, after each product is consumed.
     */
    protected $delay;

    /**
     * Constructor.
     *
     * @param Inventory $inventory The shared inventory object.
     * @param string $name The name of current consumer.
     * @param int $amount The amount of products to be consumed.
     * @param int $delay The delay, in seconds.
     */
    public function __construct(Inventory $inventory, $name, $amount, $delay = 0)
    {
        $this->inventory = $inventory;

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
            $product = $this->inventory->get($this);

            // Slows down and allow producer to produce more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}
