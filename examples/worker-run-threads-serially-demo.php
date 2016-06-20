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

class SerialDemoWork extends \Thread
{
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

$w1 = new SerialDemoWork('w-1');
$w2 = new SerialDemoWork('w-2');

$worker = new \Worker();
$worker->stack($w1);
$worker->stack($w2);

$worker->start();
$worker->shutdown();

printf("\nThreads inside a worker are executed serially\n");
printf("The thread that is stacked earlier is executed sooner.\n");
printf("%'=64s\n", '');
