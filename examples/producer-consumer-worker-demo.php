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
     * NOTE: due to issue {@link https://github.com/krakjoe/pthreads/issues/603
     * 603}, we can't pass producer instance to this method. In stead, we have
     * to pass worker name and producer name as arguments.
     *
     * @param string $workerName The name of current worker.
     * @param string $producerName The name of current producer.
     * @param string $product The product to be produced.
     * @return void
     */
    public function put($workerName, $producerName, $product)
    {
        $this->synchronized(

            function($self, $workerName, $producerName, $product){

               // Current producer should wait, as long as the inventory is full.
               while ($self->queue->count() === $self->size) {

                    printf("Producer \033[32m%s\033[0m in worker \033[33m%s\033[0m is waiting for consumer : [%s] ...\n", $producerName, $workerName, (string)$self);

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

                printf("Producer \033[32m%s\033[0m in worker \033[33m%s\033[0m is producing one product: \033[32m%s\033[0m => [%{$p1}s] ... [\033[32m%{$p2}s\033[0m%s]\n", $producerName, $workerName, $product, $before, $product, $before);

                // Now, at least one product has been put into the inventory, so
                // the inventory is no longer empty.
                // It's safe to notify all waiting consumers to come back.
                $self->notify();
            },

            $this, $workerName, $producerName, $product
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
     * @param string $workerName The name of current worker.
     * @param string $consumerName The name of current consumer.
     * @return string The product consumed.
     */
    public function get($workerName, $consumerName)
    {
        return $this->synchronized(

            function($self, $workerName, $consumerName){

                // Current consumer should wait, as long as the inventory is
                // empty.
                while (0 === $self->queue->count()) {

                    printf("Consumer \033[31m%s\033[0m in worker \033[33m%s\033[0m is waiting for producer : [%s] ...\n", $consumerName, $workerName, (string)$self);

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

                printf("Consumer \033[31m%s\033[0m in worker \033[33m%s\033[0m is consuming one product: [%s\033[31m%-{$p1}s\033[0m] => \033[31m%s\033[0m ... [%-{$p2}s]\n", $consumerName, $workerName, (string)$self, $product, $product, (string)$self);

                // Now, at least one product has been taken out of the
                // inventory, so the inventory is no longer full.
                // It's safe to notify all waiting producers to come back.
                $self->notify();

                return $product;
            },

            $this, $workerName, $consumerName
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
            $this->worker->inventory->put($this->worker->name, $this->name, $this->product);

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
            $product = $this->worker->inventory->get($this->worker->name, $this->name);

            // Slows down and allow producer to produce more products.
            if ($this->delay > 0) {

                usleep(rand(0, $this->delay * 1000000));

            }

            $remaining--;
        }
    }
}

// Multiple producers and consumers in workers
$queue = new \Threaded();
$inventory = new Inventory($queue, 5);

for ($i = 0; $i < 4; $i++) {
    $pool['p'][] = new Producer("p-$i", 5, '*', 0);
    $pool['c'][] = new Consumer("c-$i", 5, 1);
}


$workers = [];

for ($i = 0; $i < 2; $i++) {

    $workers['p'][$i] = new \Worker();
    $workers['p'][$i]->name = "pw-$i";
    $workers['p'][$i]->inventory = $inventory;
    $workers['p'][$i]->stack($pool['p'][2*$i]);
    $workers['p'][$i]->stack($pool['p'][2*$i + 1]);

    $workers['c'][$i] = new \Worker();
    $workers['c'][$i]->name = "cw-$i";
    $workers['c'][$i]->inventory = $inventory;
    $workers['c'][$i]->stack($pool['c'][2*$i]);
    $workers['c'][$i]->stack($pool['c'][2*$i + 1]);

}

for ($i = 0; $i < 2; $i++) {

    $workers['p'][$i]->start();
    $workers['c'][$i]->start();

}

for ($i = 0; $i < 2; $i++) {

    $workers['p'][$i]->shutdown();
    $workers['c'][$i]->shutdown();

}

printf("\nThread objects in workers ... \n");
printf("Four producers and four consumers have finished their jobs inside two workers ... \n");
printf("%'=128s\n\n",'');
