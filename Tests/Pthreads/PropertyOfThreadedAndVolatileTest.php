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
 * Tutorial for demonstrating the fact that threaded properties of threaded 
 * object can not be overridden, while threaded properties of volatile object 
 * can be.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class PropertyOfThreadedAndVolatileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tries to override threaded property of a threaded object. 
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Threaded members previously set to Threaded objects are immutable, cannot overwrite data
     */
    public function testOverrideThreadedPropertyOfThreadedObject()
    {
        $container = new \Threaded();
        $container->data = new \Threaded();
        $container->data = new \Threaded();
    }

    /**
     * Tries to override threaded property of a volatile object. 
     */
    public function testOverrideThreadedPropertyOfVolatileObject()
    {
        $container = new \Volatile();
        $container->data = new \Threaded();
        $container->data = new \Threaded();
    }
}
