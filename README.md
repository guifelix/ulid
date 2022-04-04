# This is a PHP [ULID](https://github.com/ulid/spec) package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guifelix/ulid.svg?style=flat-square)](https://packagist.org/packages/guifelix/ulid)
[![Tests](https://github.com/guifelix/ulid/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/guifelix/ulid/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/guifelix/ulid.svg?style=flat-square)](https://packagist.org/packages/guifelix/ulid)

PHP Library to use ULID on your application.

> - 128-bit compatibility with UUID
> - 1.21e+24 unique ULIDs per millisecond
> - Lexicographically sortable!
> - Canonically encoded as a 26 character string, as opposed to the 36 character UUID
> - [Uses Crockford's base32 for better efficiency and readability (5 bits per character)](https://github.com/ulid/spec#encoding)
> - Case insensitive
> - No special characters (URL safe)
> - Monotonic sort order (correctly detects and handles the same millisecond)

## Installation

You can install the package via composer:

```bash
composer require guifelix/php-ulid
```

## Usage

### Generate
```php
use Guifelix\Ulid;

$ulid = Ulid::generate(); // Accept boolean as a parameter for lowercase;

echo (string) $ulid; //0001EH8YAEP8CXP4AMWCHHDBHJ
echo $ulid->getTime(); //0001EH8YAE
echo $ulid->getRandomness(); //P8CXP4AMWCHHDBHJ
echo $ulid->isLowercase(); //false
echo $ulid->toTimestamp(); //1561622862
```

### Generate from timestamp
```php
use Guifelix\Ulid;

$ulid = Ulid::fromTimestamp(1561622862); // Accept boolean as a second parameter for lowercase;

echo (string) $ulid; //0001EH8YAEP8CXP4AMWCHHDBHJ
```
### Generate from string (doesn't increment randomness)
```php
use Guifelix\Ulid;

$ulid = Ulid::fromString('0001EH8YAEP8CXP4AMWCHHDBHJ'); // Accept boolean as a second parameter for lowercase;

echo (string) $ulid; //0001EH8YAEP8CXP4AMWCHHDBHJ
```

### Validate
```php
use Guifelix\Ulid;

Ulid::validate('8ZZZZZZZZZP8CXP4AMWCHHDBHI'); // Case insensitve
/**
 * validate Length, Crockford Characters and Time
 * Throws
 *  - InvalidUlidLengthException
 *  - InvalidUlidCharException
 *  - InvalidUlidTimestampException <- Max timestamp is 7ZZZZZZZZZ (281474976710655) or until the year 10889 AD :)
 *  - InvalidUlidException
 * /
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/guifelix/ulid/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Robin van der Vleuten](https://github.com/robinvdvleuten/php-ulid) - I have used most of his code for this package
- [Guilherme Maciel](https://github.com/guifelix)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
