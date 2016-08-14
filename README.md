ZerusTech Pthreads Tutorial
================================================
This is a tutorial of the [krakjoe/pthreads][1] extension v3 for ``PHP7``.

Installation
-------------

Follow the instructions below:

```bash
$ cd <php-source-directory>
$ ./configure --enable-maintainer-zts ...
$ make
$ make install
```
Download the latest 3.x release from [Pecl][3]

```bash
$ cd <php-source-directory>/ext
$ mkdir pthreads
$ tar zxf <path-to-pthreads-src-tarball> -C pthreads --strip-components=1
$ phpize
$ ./configure
$ make
$ sudo make install
$ # This will install the pthreads.so to php extension directory
$ # load pthreads.so in php.ini

```

Examples
-------------

* ``Tests/Pthreads/BasicProducerConsumerTest.php`` - a producer/consumer example with threads.

* ``Tests/Pthreads/WorkerProducerConsumer.php`` - a producer/consumer example with threads and workers.

* ``Tests/Pthreads/PoolProducerConsumer.php`` - a producer/consumer example with threads, workers and pools.

* ``Tests/Pthreads/IssueArrayPropertyTest.php`` - an example to demonstrate array can't be used as properties of threaded object.

* ``Tests/Pthreads/IssueNonThreadedPropertyTest.php`` - an example to demonstrate how non-threaded properties are broken in multi-threading environment.

* ``Tests/Pthreads/Issue602Test.php`` -  The script for reproducing [issue #602][4].

* ``Tests/Pthreads/Issue603Test.php`` -  The script for reproducing [issue #603][5].

* ``Tests/Pthreads/IssueLocalVariableTest.php`` - The script to demonstrate local variables in pthreads are destroyed.

* ``Tests/Pthreads/PropertyOfThreadedAndVolatileTest.php`` -  The script to demonstrate different accessibilities of threaded and volatile objects.

References
----------
* [The krakjoe/pthreads Project][1]
* [Issue #602][4]
* [Issue #603][5]

[1]: https://github.com/krakjoe/pthreads "The krakjoe/pthreads Project"
[2]: https://opensource.org/licenses/MIT "The MIT License (MIT)"
[3]: https://pecl.php.net/package/pthreads "Pthreads Pecl Package"
[4]: https://github.com/krakjoe/pthreads/issues/602 "Issue 602"
[5]: https://github.com/krakjoe/pthreads/issues/603 "Issue 603"

License
-------
The *ZerusTech Pthreads Tutorial* is published under the [MIT License][2].
