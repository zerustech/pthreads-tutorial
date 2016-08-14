<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Issue;

/**
 * A task that does nothing but dumping current instance by calling 
 * Dumper::dump()
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class FooTask extends \Thread
{
    private $name;

    private $dumper;

    public function __construct($name, $dumper)
    {
        $this->name = $name;

        $this->dumper = $dumper;
    }

    public function run()
    {
        printf("FooTask::run() is running: [%s][%s] ... \n", get_class($this), $this->name);
        $this->dumper->dump($this);
    }
}
