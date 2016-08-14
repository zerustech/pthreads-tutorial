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
 * This class contains a \Threaded property that is iniitialized locally.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class DataContainer extends \Threaded
{
    /**
     * @var \Threaded The data.
     */
    private $data;

    /**
     * This method creates a new DataContainer instance and initializes 
     * the ``$data`` property locally.
     * The local variable will be destroyed in pthreads API v2.x.
     */
    public function __construct()
    {
        $this->data = new \Threaded();
    }
}
