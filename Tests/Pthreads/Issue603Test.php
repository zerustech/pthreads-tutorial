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

use ZerusTech\Tutorial\Pthreads\Issue\FooTask;
use ZerusTech\Tutorial\Pthreads\Issue\BarTask;
use ZerusTech\Tutorial\Pthreads\Issue\Dumper;

/**
 * Tutorial for demonstrating issue #603.
 *
 * Check https://github.com/krakjoe/pthreads/issues/603 for details.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Issue603Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test issue #603.
     */
    public function testIssue603()
    {

        $dumper = new Dumper();

        $t1 = new FooTask('#1', $dumper);
        $t2 = new FooTask('#2', $dumper);
        $t3 = new BarTask('#3', $dumper);

        $worker = new \Worker();
        $worker->stack($t1);
        $worker->stack($t2);
        $worker->stack($t3);
        $worker->start();
        $worker->shutdown();

        // Expected:
        // -----------------------------------------------------------------------------
        // FooTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ...
        // 
        // FooTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#2] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#2] ...
        // 
        // BarTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\BarTask][#3] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\BarTask][#3] ...

        // Actual:
        // -----------------------------------------------------------------------------
        // FooTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ...
        // 
        // FooTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#2] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ...
        // 
        // BarTask::run() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#3] ... 
        // Dumper::dump() is running: [ZerusTech\Tutorial\Pthreads\Issue\FooTask][#1] ...
    }
}
