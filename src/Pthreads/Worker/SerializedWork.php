<?php
/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\Worker;

/**
 * This is a thread class to demonstrate how worker executes threads serially.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class SerializedWork extends \Thread
{
    /**
     * @var string Name of the work.
     */
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function run()
    {
        for ($i = 0; $i < 5; $i++) {

            printf("Work \033[32m%s\033[0m is running at step \033[32m%d\033[0m ...\n", $this->name, $i);

            sleep(rand(0,1));
        }
    }
}
