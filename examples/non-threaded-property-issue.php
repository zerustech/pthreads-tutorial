<?php

/**
 * This file is part of the ZerusTech package.
 *
 * (c) Michael Lee <michael.lee@zerustech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZerusTech\Tutorial\Pthreads\IssueNonThreadedProperties;

/**
 * This is a plain value object that will be used as the property of threaded objects.
 */
class PlainValueObject
{
    /**
     * @var string The value.
     */
    protected $value;

    /**
     * Constructor.
     * @param string $value The value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value.
     * @return string The value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value.
     * @param string $value The value.
     * @return PlainObject Current instance.
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Dumps current plain object.
     * @return void.
     */
    public function report()
    {
        printf("value: %s\n", $this->value);
    }
}

/**
 * This is a threaded object that actually won't work.
 *
 * This is because plain (non-threaded) objects can't be used as members of a
 * threaded object. You can only pass scalar variables or threaded objects to a
 * threaded object.
 *
 * An object becomes threaded if it extends the {@link Threaded} class.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class BadThreadedObject extends \Threaded
{
    /**
     * Since it's a plain object, it won't work as expected.
     *
     * @var PlainValueObject $data The data container.
     */
    private $data;

    /**
     * Constructor.
     *
     * @param array $data The data container.
     */
    public function __construct(PlainValueObject $data)
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
        $this->data->report();
    }

    /**
     * Sets the value object.
     *
     * @param PlainValueObject $data The plain value object.
     * @return BadThreadedObject Current instance.
     */
    public function setData(PlainValueObject $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the value object.
     *
     * @return string The shifted element.
     */
    public function getData()
    {
        return $this->data;
    }
}

// The initial data container.
$data = new PlainValueObject('hello');

$q = new BadThreadedObject($data);

$data->setValue('world');

// Expected result: 'value: world'
// Actual result: 'value: hello'
// So it's clear that passing plain object to threaded object won't work.
$q->report();

printf("%'=64s\n", '');

/**
 * This is a threaded value object that will be used as the property of threaded objects.
 */
class ThreadedValueObject extends \Threaded
{
    /**
     * @var string The value.
     */
    protected $value;

    /**
     * Constructor.
     * @param string $value The value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value.
     * @return string The value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value.
     * @param string $value The value.
     * @return PlainObject Current instance.
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Dumps current plain object.
     * @return void.
     */
    public function report()
    {
        printf("value: %s\n", $this->value);
    }
}

/**
 * This is a threaded object that actually works.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class ThreadedObject extends \Threaded
{
    /**
     * @var ThreadedValueObject $data The data container.
     */
    private $data;

    /**
     * Constructor.
     *
     * @param array $data The data container.
     */
    public function __construct(ThreadedValueObject $data)
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
        $this->data->report();
    }

    /**
     * Sets the value object.
     *
     * @param PlainValueObject $data The plain value object.
     * @return BadThreadedObject Current instance.
     */
    public function setData(ThreadedValueObject $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the value object.
     *
     * @return string The shifted element.
     */
    public function getData()
    {
        return $this->data;
    }
}

// The initial data container.
$data = new ThreadedValueObject('hello');

$q = new ThreadedObject($data);

$data->setValue('world');

// Expected result: 'value: world'
// Actual result: 'value: world'
// So it's clear that passing plain object to threaded object won't work.
$q->report();

printf("%'=64s\n", '');
