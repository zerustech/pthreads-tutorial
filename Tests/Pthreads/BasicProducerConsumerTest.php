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
class BasicProducerConsumerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * One producer and one consumer as standalone variables.
     */
    public function testOneProducerAndOneConsumer()
    {
        $inventory = new Inventory(5);

        $p1 = new Producer($inventory, 'p1', 5, '*', 0);
        $c1 = new Consumer($inventory, 'c1', 5, 1);

        $p1->start();
        $c1->start();

        $p1->join();
        $c1->join();

        $this->AssertTrue(true);
        printf("\nOne producer and one consumer have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }

    /**
     * Two producers and two consumers as standalone variables.
     */
    public function testTwoProducersAndTwoConsumers()
    {
        $inventory = new Inventory(5);

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

        $this->AssertTrue(true);
        printf("\nTwo producers and two consumers have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }

    /**
     * Two producers and two consumers as array elements.
     */
    public function testMultipleProducersAndConsumersInArray()
    {
        $inventory = new Inventory(5);

        $pool = [];

        for ($i = 0; $i < 2; $i++) {
            $pool['p'][] = new Producer($inventory, "p$i", 5, '*', 0);
            $pool['c'][] = new Consumer($inventory, "c$i", 5, 1);
        }

        for ($i = 0; $i < 2; $i++) {
            $pool['p'][$i]->start();
            $pool['c'][$i]->start();
        }

        // Issue #602 has been fixed in pthreads API v3, so there is no need to
        // do the join here.
        /*
        for ($i = 0; $i < 2; $i++) {
            $pool['p'][$i]->join();
            $pool['c'][$i]->join();
        }
        */

        $this->AssertTrue(true);
        printf("\nTwo producers and two consumers in an array have finished their jobs ... \n");
        printf("%'=80s\n\n",'');
    }
}
