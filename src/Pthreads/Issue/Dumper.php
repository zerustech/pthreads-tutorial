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
 * This class dumps an object's class and name at runtime.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class Dumper extends \Threaded
{
    /**
     * Dumps the object's class name and name.
     *
     * @param mixed $object The object to be investigated.
     */
    public function dump($object)
    {
        printf("Dumper::dump() is running: [%s][%s] ...\n\n", get_class($object), $object->name);
    }
}
