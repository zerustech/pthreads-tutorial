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

/**
 * Tutorial for demonstrating issue #602.
 *
 * Check https://github.com/krakjoe/pthreads/issues/602 for details.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Issue602Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test issue #602.
     */
    public function testIssue602()
    {
        $inventory = new Inventory(5);
        $pool = [];
        $pool[] = new Producer($inventory, 'p#1', 5);
        $pool[0]->start();
        // Issue #602 has been fixed in pthreads API v3, so there is no need to 
        // do the join here.
        // $pool[0]->join();
    }
}
