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
use ZerusTech\Tutorial\Pthreads\Worker\Producer;
use ZerusTech\Tutorial\Pthreads\Worker\Consumer;
use ZerusTech\Tutorial\Pthreads\Worker\SerializedWork;

/**
 * Tutorial for producer and consumer instances organized in pthreads workers.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class WorkerProducerConsumerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Multiple producers and consumer in workers.
     */
    public function testMultipleProducersAndConsumersInWorkers()
    {
        $inventory = new Inventory(5);

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
    }

    /**
     * Threads inside a worker are executed serially.
     */
    public function testSerializedWork()
    {
        $w1 = new SerializedWork('w-1');
        $w2 = new SerializedWork('w-2');

        $worker = new \Worker();
        $worker->stack($w1);
        $worker->stack($w2);

        $worker->start();
        $worker->shutdown();

        printf("\nThreads inside a worker are executed serially\n");
        printf("The thread that was stacked earlier is executed sooner.\n");
        printf("%'=64s\n", '');
    }
}
