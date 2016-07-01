<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\IssueLocalVariable;

/**
 * This is a threaded queue to demonstrate local variables get destroyed in 
 * pthreads v2.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class ThreadedQueue extends \Threaded
{
    /**
     * @var \Threaded $data The data container.
     */
    private $data;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // This is a local variable, which will be destroyed in pthreads v2.
        $this->data = new \Threaded();
    }

    /**
     * Dumps the contents of the data container.
     *
     * @return void
     */
    public function report()
    {
        $data = [];

        foreach ($this->data as $element) {

            $data[] = $element;

        }

        printf("[%s]\n", implode(',', $data));
    }

    /**
     * Pushes an element into the data container.
     *
     * @param string $element The element to be pushed.
     * @return void
     */
    public function push($element)
    {
        $this->data[] = $element;
    }

    /**
     * Shifts an element from the head of the data container.
     *
     * @return string The shifted element.
     */
    public function shift()
    {
        return array_shift($this->data);
    }
}

// The initial data container.
try {

    $q = new ThreadedQueue();
    $q->push('h');
    $q->push('e');
    $q->push('l');
    $q->push('l');
    $q->push('o');
    $q->report();

} catch (\RuntimeException $e) {

    printf("%s\n", $e->getMessage());

}

// Expected result: ['h', 'e', 'l', 'l', 'o']
// Actual result: pthreads detected an attempt to connect to a Threaded which 
// has already been destroyed
// Local variables are destroyed in pthreads v2
printf("%'=64s\n", '');
