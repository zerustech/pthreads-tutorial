ZerusTech Pthreads Tutorial
================================================
This is a tutorial of the [krakjoe/pthreads][1] extension v2 for ``PHP5``.

Installation
-------------

Follow the instructions below:

```bash
$ cd <php-source-directory>
$ ./configure --enable-maintainer-zts ...
$ make 
$ make install
```
Download the latest 2.x release from [Pecl][3] 

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

* ``producer-consumer-basic-demo.php`` - a producer/consumer example with
  threads.
* ``producer-consumer-worker-demo.php`` - a producer/consumer example with
  threads and workers. 
* ``producer-consumer-pool-demo.php`` - a producer/consumer example with
  threads, workers and pools.
* ``worker-run-threads-serially-demo.php`` - a example to demonstrate how
  threads inside a worker are executed serially.
* ``array-property-issue.php`` - a example to demonstrate how array properties
  are broken in multi-threading environment.
* ``issue-602.php`` -  The script for reproducing [issue #602][4].
* ``issue-603.php`` -  The script for reproducing [issue #603][5].
* ``local-variable-issue.php`` - The script to demonstrate local variables are
  destroyed in pthreads v2.

References
----------
* [The krakjoe/pthreads Project][1]
* [Issue #602][4]
* [Issue #603][5]

[1]:  https://github.com/krakjoe/pthreads "The krakjoe/pthreads Project"
[2]:  https://opensource.org/licenses/MIT "The MIT License (MIT)"
[3]:  https://pecl.php.net/package/pthreads "Pthreads Pecl Package"
[4]:  https://github.com/krakjoe/pthreads/issues/602 "Issue 602"
[5]:  https://github.com/krakjoe/pthreads/issues/603 "Issue 603"

License
-------
The *ZerusTech Pthreads Tutorial* is published under the [MIT License][2].
