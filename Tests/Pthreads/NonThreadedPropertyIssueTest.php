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
 * Tutorial for demonstrating the fact that it does not work to pass
 * non-threaded property to threaded object.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class NonThreadedPropertyIssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test with array property in threaded object.
     */
    public function testNonThreadedPropertyInThreadedObject()
    {
        // The initial data container.
        $data = new \Stdclass();

        $data->value = 'hello';

        $container = new \Threaded();

        $container->data = $data;

        $container->data->value = 'world';

        // You might thought $container->data->value is equal to: 'world',
        // but in fact it is: 'hello'
        // So it's clear that passing non-threaded property to threaded object won't work.
        $this->assertEquals('hello', $container->data->value);

        printf("\nIt does not work to pass non-threaded property to a threaded object.\n");
        printf("%'=64s\n", '');
    }

    /**
     * Test with threaded property in threaded object.
     */
    public function testThreadedPropertyInThreadedObject()
    {
        // The initial data container.
        $data = new \Threaded();
        $data->value = 'hello';

        $container = new \Threaded();
        $container->data = $data;
        $container->data->value = 'world';

        // Expected result: 'world'
        // Actual result: 'world'
        $this->assertEquals('world', $container->data->value);

        printf("\nIt works to pass a threaded property to a threaded object.\n");
        printf("%'=64s\n", '');
    }
}
