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

use ZerusTech\Tutorial\Pthreads\Issue\DataContainer;

/**
 * Tutorial for demonstrating local variable issue.
 *
 * In pthreads API v2.x, local variables will be destroyed unexpectly.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class IssueLocalVariableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage  pthreads detected an attempt to connect to a Threaded which has already been destroyed
     */
    public function testIssueLocalVariable()
    {
        $container = new DataContainer();

        $container->data[] = 'hello';
    }
}
