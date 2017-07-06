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
class BarTask extends \Thread
{
    /** @var string */
    private $name;

    /** @var Dumper */
    private $dumper;

    public function __construct($name, Dumper $dumper)
    {
        $this->name = $name;

        $this->dumper = $dumper;
    }

    public function run()
    {
        printf("BarTask::run() is running: [%s][%s] ... \n", get_class($this), $this->name);
        $this->dumper->dump($this);
    }
}
