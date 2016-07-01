<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\IssueArrayProperties;

/**
 * This is a threaded queue that actually won't work.
 *
 * This is because neither array nor plain (non-threaded) objects can be used as
 * members of a threaded object. You can only pass scalar variables or threaded
 * objects to a threaded object.
 *
 * An object becomes threaded if it extends the {@link Threaded} class.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BadThreadedQueue extends \Threaded
{
    /**
     * Since it's an array, it won't work as expected.
     *
     * @var array $data The data container.
     */
    private $data;

    /**
     * Constructor.
     *
     * @param array $data The data container.
     */
    public function __construct($data)
    {
        $this->data = $data;
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
$data = ['a', 'b', 'c'];

$q = new BadThreadedQueue($data);

// Pushes 'd' to the end of the data container.
$q->push('d');

// Expected result: ['a', 'b', 'c', 'd']
// Actual result: ['a', 'b', 'c']
// So it's clear that passing array to threaded object won't work.
$q->report();

printf("%'=64s\n", '');

/**
 * This is a threaded queue that works.
 *
 * In this class, the data container is also a threaded object.
 *
 * An object becomes threaded if it extends the {@link Threaded} class.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class ThreadedQueue extends \Threaded
{
    /**
     * @var Threaded $data The threaded data container.
     */
    private $data;

    /**
     * Constructor.
     *
     * @param Threaded $data The data container.
     */
    public function __construct(\Threaded $data)
    {
        $this->data = $data;
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
     * Pushes the given element to the end of the data container.
     *
     * @param string $element The element to be pushed.
     * @return void
     */
    public function push($element)
    {
        $this->data[] = $element;
    }

    /**
     * Shifts an element from the beginning of the data container.
     *
     * @return string The shifted element.
     */
    public function shift()
    {
        return $this->data->shift();
    }
}

// Initializes the data container.
$data = new \Threaded();
$data[] = 'a';
$data[] = 'b';
$data[] = 'c';

// Passing the data container to the queue.
$q = new ThreadedQueue($data);

// Pushes 'd' to the end of the data container.
$q->push('d');

// Expected result: ['a', 'b', 'c', 'd']
// Actual result: ['a', 'b', 'c', 'd']
$q->report();

printf("%'=64s\n", '');
