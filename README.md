# SPF for PHP
SPFPHP is a library designed to make it easy and intuitive to implement
[spfjs](https://github.com/youtube/spfjs/) for your own server using php.
It handles everything related to SPF on the server and it makes sure that
the client will receive the correct information.

## Requirement
The only requirement for this library is that your server is using
**PHP 5.3.0** or newer though it's possible that it would still work with
older versions, but some of the API might not work.

## Features
This library currently support most of the specification of SPF.

* Full support for single SPF response.
* Partial support for multipart/chunked SPF response (planned to implement
  full support in the future).

## Get Started
Before anything else you need to understand [spfjs](https://github.com/youtube/spfjs/).

SPFPHP has no dependencies and is a single standalone file.

To get started, clone the project, build the main SPFPHP file, and copy it
to your server where your PHP files can access it:

```shell
$ git clone https://github.com/YePpHa/spfphp.git
$ cd spfphp
$ npm install
$ grunt
$ cp build/spf.php YOUR_PHP_DIR/
```

You will need to make sure that you have [node.js](http://nodejs.org/)
and [grunt](http://gruntjs.com/) installed.

For examples on how to implement SPFPHP for your server application
you can take a look at the directory `./examples`.

## API
To be added soon.

## License
MIT
Copyright 2014 Jeppe Rune Mortensen