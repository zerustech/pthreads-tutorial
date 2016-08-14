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

/**
 * Tutorial for demonstrating the fact that it does not work to pass array
 * property to threaded object.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class IssueArrayPropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test with array property in threaded object.
     */
    public function testArrayPropertyInThreadedObject()
    {
        // The initial data container.
        $data = ['a', 'b', 'c'];

        $container = new \Threaded();

        $container->data = $data;

        $container->data[] = 'd';

        // You might thought $container->data is equal to: ['a', 'b', 'c', 'd'],
        // but in fact it is: ['a', 'b', 'c']
        // So it's clear that passing array to threaded object won't work.
        $this->assertEquals(['a', 'b', 'c'], $container->data);

        printf("\nIt does not work to pass an array property to a threaded object.\n");
        printf("%'=64s\n", '');
    }

    /**
     * Test with threaded property in threaded object.
     */
    public function testThreadedPropertyInThreadedObject()
    {
        // The initial data container.
        $data = new \Threaded();
        $data[] = 'a';
        $data[] = 'b';
        $data[] = 'c';

        $container = new \Threaded();
        $container->data = $data;
        $container->data[] = 'd';

        // Expected result: ['a', 'b', 'c', 'd']
        // Actual result: ['a', 'b', 'c', 'd']
        $this->assertEquals(['a', 'b', 'c', 'd'], array_values((array)$container->data));

        printf("\nIt works to pass a threaded property to a threaded object.\n");
        printf("%'=64s\n", '');
    }
}
