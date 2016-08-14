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
 * A demo producer.
 *
 * Producer produces and puts products into the shared inventory.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Producer extends \Thread
{
    /**
     * @var Threaded The shared inventory.
     */
    private $inventory;

    /**
     * @var string The name of current producer.
     */
    private $name;

    /*
     * For demonstration purpose, we use string as the product type.
     * However, you can use anything type for it, as long as it has implemented
     * the ``__toString()`` method.
     *
     * @var string The product template.
     */
    private $product;

    /**
     * @var int The amount of products to be produced.
     */
    private $amount;


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
     * @param string $name The name of current producer.
     * @param int $amount The amount of products to be produced.
     * @param string $product The template / prototype for producing products.
     * @param int $delay The delay, in seconds.
     */
    public function __construct(\Threaded $inventory, $name, $amount, $product = '*', $delay = 0)
    {
        $this->inventory = $inventory;

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
            $this->inventory->put($this->name, $this->product);

            // Slows down and allow consumer to consume more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}
