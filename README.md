# Obrero (Worker)

The current PHP database connection class provided by professor Knautz for use in the Volga project is not up to date with current PHP standards. It makes use of the [original](http://php.net/manual/en/book.mysql.php) MySQL API. This API was deprecated as of PHP 5.5.0 and should **not** be used.

This class aims to replace the original with a more modern one. This databse connection class should **NOT** be used in production code whatsoever. Instead, use an already well-tested database connection component such as Laravel's [Illuminate Database](https://github.com/illuminate/database). 

## Quickstart

Ensure you have at minimum PHP 5.4.0. In theory, it should work like so:

```php
$connection = Obrero::getInstance();
$connection->connect(); // returns true on success
```

Has not been tested yet. It may or may not work (probably not).

## License

MIT  
Copyright 2015 Francisco mateo