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
    public function __construct($size)
    {
        $this->queue = new \Threaded();

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
     * NOTE: Issue {@link https://github.com/krakjoe/pthreads/issues/603
     * 603} has been fixed in pthreads API v3, it's possible to pass object as
     * method arguments.
     *
     * @param string $producer The producer.
     * @param string $product The product to be produced.
     * @return void
     */
    public function put($producer, $product)
    {
        $this->synchronized(

            function($self, $producer, $product){

               // Current producer should wait, as long as the inventory is full.
               while ($self->queue->count() === $self->size) {

                   if (null !== $producer->worker) {

                       printf("Producer \033[32m%s\033[0m in worker \033[33m%s\033[0m is waiting for consumer : [%s] ...\n", $producer->name, $producer->worker->name, (string)$self);

                   } else {

                       printf("Producer \033[32m%s\033[0m is waiting for consumer : [%s] ...\n", $producer->name, (string)$self);
                   }

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

                if (null !== $producer->worker) {

                    printf("Producer \033[32m%s\033[0m in worker \033[33m%s\033[0m is producing one product: \033[32m%s\033[0m => [%{$p1}s] ... [\033[32m%{$p2}s\033[0m%s]\n", $producer->name, $producer->worker->name, $product, $before, $product, $before);

                } else {

                    printf("Producer \033[32m%s\033[0m is producing one product: \033[32m%s\033[0m => [%{$p1}s] ... [\033[32m%{$p2}s\033[0m%s]\n", $producer->name, $product, $before, $product, $before);
                }

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
     * @param string $workerName The name of current worker.
     * @param string $consumerName The name of current consumer.
     * @return string The product consumed.
     */
    public function get($consumer)
    {
        return $this->synchronized(

            function($self, $consumer){

                // Current consumer should wait, as long as the inventory is
                // empty.
                while (0 === $self->queue->count()) {

                    if (null !== $consumer->worker) {

                        printf("Consumer \033[31m%s\033[0m in worker \033[33m%s\033[0m is waiting for producer : [%s] ...\n", $consumer->name, $consumer->worker->name, (string)$self);

                    } else {

                        printf("Consumer \033[31m%s\033[0m is waiting for producer : [%s] ...\n", $consumer->name, (string)$self);
                    }

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

                if (null !== $consumer->worker) {

                    printf("Consumer \033[31m%s\033[0m in worker \033[33m%s\033[0m is consuming one product: [%s\033[31m%-{$p1}s\033[0m] => \033[31m%s\033[0m ... [%-{$p2}s]\n", $consumer->name, $consumer->worker->name, (string)$self, $product, $product, (string)$self);

                } else {

                    printf("Consumer \033[31m%s\033[0m is consuming one product: [%s\033[31m%-{$p1}s\033[0m] => \033[31m%s\033[0m ... [%-{$p2}s]\n", $consumer->name, (string)$self, $product, $product, (string)$self);
                }

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
