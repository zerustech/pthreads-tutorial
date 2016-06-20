<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Thread;

/**
 * The inventory class.
 *
 * This is a threaded data container where producers put products into and
 * consumers get products from. It holds the products in an internal threaded
 * queue.
 *
 * Only threaded (descended from Threaded class) objects and scalar variables
 * can be set as members of a threaded object.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Inventory extends \Threaded
{
    /**
     * @var int The size of the product queue for containing products.
     */
    private $size;

    /**
     * This internal queue is also a threaded object, therefor it's a valid
     * property of current inventory object.
     *
     * @var Threaded The product queue.
     */
    private $queue;

    /**
     * Constructor.
     *
     * @param Threaded $queue The product queue.
     * @param int $size The inventory capacity.
     */
    public function __construct(\Threaded $queue, $size)
    {
        $this->queue = $queue;

        $this->size = $size;
    }

    /**
     * Converts the product queue to an array.
     *
     * @return array The converted array.
     */
    public function toArray()
    {
        $products = [];

        foreach ($this->queue as $product) {

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Converts the product queue to string.
     *
     * @return string The string representative of current instance.
     */
    public function __toString()
    {
        return implode('', $this->toArray());
    }

    /**
     * Puts one product into the inventory.
     *
     * This method is ``synchronized`` on current inventory instance. So
     * when putting a product in, the inventory object is locked. Therefore,
     * other attempts for putting or getting product will be blocked, until:
     * - the product has been put in and the lock is released; or
     * - the producer releases the lock and waits for a consumer to consumes a
     * product, because currently the inventory is full.
     *
     * @param Producer $producer The producer of current product.
     * @param string $product The product to be produced.
     * @return void
     */
    public function put($producer, $product)
    {
        $this->synchronized(

            function($self, $producer, $product){

               // Current producer should wait, as long as the inventory is full.
               while ($self->queue->count() === $self->size) {

                    printf("Producer \033[32m%s\033[0m is waiting for consumer : [%s] ...\n", $producer->name, (string)$self);

                    // Waits on the inventory object.
                    // Moves current thread to the "waiting room" of the
                    // inventory object.
                    $self->wait();
                }

                // Now, at least one product has been consumed.
                // And the consumer has notified all waiting producers to come
                // back: moving from the "waiting room" to the "producing room".

                $before = (string)$self;

                $p1 = $self->size;

                $p2 = $self->size - $self->queue->count();

                $self->queue[] = $product;

                printf("Producer \033[32m%s\033[0m is producing one product: \033[32m%s\033[0m => [%{$p1}s] ... [\033[32m%{$p2}s\033[0m%s]\n", $producer->name, $product, $before, $product, $before);

                // Now, at least one product has been put into the inventory, so
                // the inventory is no longer empty.
                // It's safe to notify all waiting consumers to come back.
                $self->notify();
            },

            $this, $producer, $product
        );
    }

    /**
     * Takes one product out of the inventory.
     *
     * This method is ``synchronized`` on the inventory object. So when a
     * product is being consumed, the inventory object is locked. Therefore,
     * other attempts for putting or consuming products will be blocked, until:
     * - the product has been consumed and the lock is released; or
     * - the consumer releases the lock and waits for a producer to produce a
     * product, because currently the inventory is empty.
     *
     * @param Consumer Consumer of current product.
     * @return string The product consumed.
     */
    public function get($consumer)
    {
        return $this->synchronized(

            function($self, $consumer){

                // Current consumer should wait, as long as the inventory is
                // empty.
                while (0 === $self->queue->count()) {

                    printf("Consumer \033[31m%s\033[0m is waiting for producer : [%s] ...\n", $consumer->name, (string)$self);

                    // Waits on the inventory object.
                    // Moves current thread to the "waiting room" of the
                    // inventory object.
                    $self->wait();
                }

                $before = (string)$self;

                $p2 = $this->size;

                // Now, at least one product has been produced.
                // And the producer has notified the waiting consumer to come
                // back: moving from the "waiting room" to the "Consuming room".
                $product = $self->queue->shift();

                $p1 = $self->size - $self->queue->count();

                printf("Consumer \033[31m%s\033[0m is consuming one product: [%s\033[31m%-{$p1}s\033[0m] => \033[31m%s\033[0m ... [%-{$p2}s]\n", $consumer->name, (string)$self, $product, $product, (string)$self);

                // Now, at least one product has been taken out of the
                // inventory, so the inventory is no longer full.
                // It's safe to notify all waiting producers to come back.
                $self->notify();

                return $product;
            },

            $this, $consumer
        );
    }
}

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
            $this->inventory->put($this, $this->product);

            // Slows down and allow consumer to consume more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}

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
     * @var Threaded The shared inventory.
     */
    private $inventory;

    /**
     * @var string The name of current consumer.
     */
    private $name;

    /**
     * @var int The amount of products to be consumed.
     */
    private $amount;

    /**
     * This property is used to slow down the thread to simulate variant
     * parallel circumstances.
     *
     * @var int The amount of delay, in seconds, after each product is consumed.
     */
    private $delay;

    /**
     * Constructor.
     *
     * @param Threaded $inventory The shared inventory object.
     * @param string $name The name of current consumer.
     * @param int $amount The amount of products to be consumed.
     * @param int $delay The delay, in seconds.
     */
    public function __construct(\Threaded $inventory, $name, $amount, $delay = 0)
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

// One producer and one consumer
// The product queue must be defined outside the inventory object.
// otherwise, it will be reset to ``null``.
$queue = new \Threaded();

$inventory = new Inventory($queue, 5);

$p1 = new Producer($inventory, 'p1', 5, '*', 0);
$c1 = new Consumer($inventory, 'c1', 5, 1);

printf("\n");

$p1->start();
$c1->start();

$p1->join();
$c1->join();

printf("\nOne producer and one consumer have finished their jobs ... \n");
printf("%'=64s\n\n",'');

// Multiple producers and consumers
$queue = new \Threaded();
$inventory = new Inventory($queue, 5);

$p1 = new Producer($inventory, 'p1', 5, '*', 0);
$p2 = new Producer($inventory, 'p2', 5, '*', 0);
$c1 = new Consumer($inventory, 'c1', 5, 1);
$c2 = new Consumer($inventory, 'c2', 5, 1);

printf("\n");

$p1->start();
$p2->start();
$c1->start();
$c2->start();

$p1->join();
$p2->join();
$c1->join();
$c2->join();

printf("\nTwo producers and two consumers have finished their jobs ... \n");
printf("%'=64s\n\n",'');

// Multiple producers and consumers in an array
$queue = new \Threaded();
$inventory = new Inventory($queue, 5);

$pool = [];

for ($i = 0; $i < 2; $i++) {
     $pool['p'][] = new Producer($inventory, "p$i", 5, '*', 0);
     $pool['c'][] = new Consumer($inventory, "c$i", 5, 1);
}

for ($i = 0; $i < 2; $i++) {
    $pool['p'][$i]->start();
    $pool['c'][$i]->start();
}

// Due to issue #602, at least one thread must be joined, otherwise, a segment
// fault occurs.
// NOTE: $pool['p'][0]->join(), this won't work,
// Try other threads, instead
$pool['p'][1]->join();

printf("\nThread objects in array ... \n");
printf("Two producers and two consumers have finished their jobs ... \n");
printf("%'=64s\n\n",'');
