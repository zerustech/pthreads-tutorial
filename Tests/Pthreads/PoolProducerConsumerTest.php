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
use ZerusTech\Tutorial\Pthreads\Pool\Producer;
use ZerusTech\Tutorial\Pthreads\Pool\Consumer;

/**
 * Tutorial for producer and consumer instances organized in pthreads pools.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PoolProducerConsumerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Multiple producers and consumer in pools.
     */
    public function testMultipleProducersAndConsumersInPools()
    {
        $inventory = new Inventory(5);

        $producers = new \Pool(2, 'ZerusTech\Tutorial\Pthreads\Pool\InventoryWorker', ['pw-', $inventory]);
        $producers->name = 'p-pool-1';

        $consumers = new \Pool(2, 'ZerusTech\Tutorial\Pthreads\Pool\InventoryWorker', ['cw-', $inventory]);
        $consumers->name = 'c-pool-1';

        $list = [];

        for ($i = 0; $i < 4; $i++) {
            $list['p'][] = new Producer("p-$i", 5, '*', 0);
            $list['c'][] = new Consumer("c-$i", 5, 1);
        }

        for ($i = 0; $i < 4; $i++) {
            $producers->submit($list['p'][$i]);
            $consumers->submit($list['c'][$i]);
        }

        $remainedProducers = 4;

        $remainedConsumers = 4;

        // Use loop to collect all producers
        while ($remainedProducers > 0) {

            $producers->collect(

                function($work) use (&$remainedProducers){

                    if ($work->isGarbage()) {

                        $remainedProducers--;

                    }

                    return $work->isGarbage();
                }

            );

            // Adds a random delay to avoid dead lock
            usleep(rand(0,1000));
        }

        // Use loop to collect all consumers
        while ($remainedConsumers > 0) {

            $consumers->collect(

                function($work) use (&$remainedConsumers){

                    if ($work->isGarbage()) {

                        $remainedConsumers--;

                    }

                    return $work->isGarbage();
                }

            );

            // Adds a rando delay to avoid dead lock
            usleep(rand(0,1000));
        }

        $producers->shutdown();

        $consumers->shutdown();

        printf("\nThread objects in pools ... \n");
        printf("Four producers and four consumers have finished their jobs ... \n");
        printf("Producers and consumers are submitted to two pools: the producers pool and the consumers pool \n");
        printf("Producers and consumers are distributed to two workers inside their pools.\n");
        printf("%'=128s\n\n",'');
    }
}
