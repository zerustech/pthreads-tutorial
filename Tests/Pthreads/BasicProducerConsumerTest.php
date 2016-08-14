<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Tests\Pthreads;

use ZerusTech\Tutorial\Pthreads\Basic\Inventory;
use ZerusTech\Tutorial\Pthreads\Basic\Producer;
use ZerusTech\Tutorial\Pthreads\Basic\Consumer;

/**
 * Basic tutorial for producer and consumer.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BasicProducerConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * One producer and one consumer as standalone variables.
     */
    public function testOneProducerAndOneConsumer()
    {
        // The product queue must be defined outside the inventory object.
        // otherwise, it will be reset to ``null``.
        $queue = new \Threaded();

        $inventory = new Inventory($queue, 5);

        $p1 = new Producer($inventory, 'p1', 5, '*', 0);
        $c1 = new Consumer($inventory, 'c1', 5, 1);

        $p1->start();
        $c1->start();

        $p1->join();
        $c1->join();

        printf("\nOne producer and one consumer have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }

    /**
     * Two producers and two consumers as standalone variables.
     */
    public function testTwoProducersAndTwoConsumers()
    {
        $queue = new \Threaded();
        $inventory = new Inventory($queue, 5);

        $p1 = new Producer($inventory, 'p1', 5, '*', 0);
        $p2 = new Producer($inventory, 'p2', 5, '*', 0);
        $c1 = new Consumer($inventory, 'c1', 5, 1);
        $c2 = new Consumer($inventory, 'c2', 5, 1);

        $p1->start();
        $p2->start();
        $c1->start();
        $c2->start();

        $p1->join();
        $p2->join();
        $c1->join();
        $c2->join();

        printf("\nTwo producers and two consumers have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }

    /**
     * Two producers and two consumers as array elements.
     */
    public function testMultipleProducersAndConsumersInArray()
    {
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
        // $pool['p'][1]->join();
        for ($i = 0; $i < 2; $i++) {
            $pool['p'][$i]->join();
            $pool['c'][$i]->join();
        }

        printf("\nTwo producers and two consumers in an array have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }
}
