# Laravel Wikipedia Grabber

[<img src="https://user-images.githubusercontent.com/1286821/43083932-4915853a-8ea0-11e8-8983-db9e0f04e772.png" alt="Become a Patron" width="160" />](https://patreon.com/dmitryivanov)

[![StyleCI](https://styleci.io/repos/117998599/shield?branch=5.7&style=flat)](https://styleci.io/repos/117998599)
[![Build Status](https://travis-ci.org/dmitry-ivanov/laravel-wikipedia-grabber.svg?branch=5.7)](https://travis-ci.org/dmitry-ivanov/laravel-wikipedia-grabber)
[![Coverage Status](https://coveralls.io/repos/github/dmitry-ivanov/laravel-wikipedia-grabber/badge.svg?branch=5.7)](https://coveralls.io/github/dmitry-ivanov/laravel-wikipedia-grabber?branch=5.7)

[![Latest Stable Version](https://poser.pugx.org/illuminated/wikipedia-grabber/v/stable)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![Latest Unstable Version](https://poser.pugx.org/illuminated/wikipedia-grabber/v/unstable)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![Total Downloads](https://poser.pugx.org/illuminated/wikipedia-grabber/downloads)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![License](https://poser.pugx.org/illuminated/wikipedia-grabber/license)](https://packagist.org/packages/illuminated/wikipedia-grabber)

Grab Wikipedia (or another MediaWiki) page in Laravel.

| Laravel | Wikipedia Grabber                                                            |
| ------- | :--------------------------------------------------------------------------: |
| 5.5.*   | [5.5.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.5) |
| 5.6.*   | [5.6.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.6) |
| 5.7.*   | [5.7.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.7) |

## Table of contents

- [Usage](#usage)
- [Formats](#formats)
- [Languages](#languages)
- [Methods](#methods)
- [Preview](#preview)
- [Random](#random)
- [Advanced](#advanced)
  - [Configuration](#configuration)
  - [Get by id](#get-by-id)
  - [MediaWiki](#mediawiki)
  - [Modifications](#modifications)
  - [Caching, caching, caching!](#caching-caching-caching)
- [License](#license)

## Usage

1. Install the package via Composer:

    ```shell
    composer require "illuminated/wikipedia-grabber:5.7.*"
    ```

2. Use `Wikipedia` class:

    ```php
    use Wikipedia;

    echo (new Wikipedia)->page('Donald Trump');
    ```

## Formats

Supported formats:

- `plain` (default)
- `bulma` (see [Bulma](https://bulma.io))
- `bootstrap` (see [Bootstrap 3](https://getbootstrap.com/docs/3.3/), [Bootstrap 4](https://getbootstrap.com))

Change format in your config (see [Configuration](#configuration)):

```php
'format' => 'bulma',
```

Or use proper helper methods on the fly:

```php
echo (new Wikipedia)->page('Donald Trump')->bootstrap();
```

## Languages

> Only `en` and `ru` languages are supported now.

English is the default language. But you can change it:

```php
echo (new Wikipedia('ru'))->page('Donald Trump');
```

## Methods

You get an object returned, so:

```php
$page = (new Wikipedia)->page('President Trump');

if ($page->isSuccess()) {
    echo $page->getId();    // 4848272
    echo $page->getTitle(); // Donald Trump
    echo $page;             // These two are the same
    echo $page->getBody();  // These two are the same
}
```

Here is an example of the successfully grabbed page:

```php
$page = (new Wikipedia)->page('Donald Trump');

$page->isSuccess();         // true
$page->isMissing();         // false
$page->isInvalid();         // false
$page->isDisambiguation();  // false
```

And here is an example of the successfully grabbed disambiguation page:

```php
$page = (new Wikipedia)->page('David Taylor');

$page->isSuccess();         // true
$page->isInvalid();         // false
$page->isMissing();         // false
$page->isDisambiguation();  // true
```

## Preview

The preview consists of an intro section and the main image. It has the same API:

```php
echo (new Wikipedia)->preview('Donald Trump');
```

## Random

You can grab the random page:

```php
echo (new Wikipedia)->randomPage();
```

Or the random preview:

```php
echo (new Wikipedia)->randomPreview();
```

## Advanced

### Configuration

You can publish config to override some settings:

```shell
php artisan vendor:publish --provider="Illuminated\Wikipedia\ServiceProvider"
```

It is highly recommended to override `user_agent`, at least:

```php
'user_agent' => 'Application Name (http://example.com; foo@example.com)',
```

### Get by id

Just pass an integer to the method:

```php
echo (new Wikipedia)->page(4848272);
```

The same is true for the preview method:

```php
echo (new Wikipedia)->preview(4848272);
```

### MediaWiki

You are not limited to Wikipedia. Grab the pages from any MediaWiki site:

```php
use MediaWiki;

echo (new MediaWiki('https://foopedia.org/w/api.php'))->page('Donald Trump');
```

### Modifications

You can append section to the end:

```php
echo (new Wikipedia)
        ->page('Donald Trump')
        ->append('Hey!', 'Please, donate me $1M, Mr. Trump!');
```

Or take the full control and change sections as you wish:

```php
$page = (new Wikipedia)->page('Donald Trump');

$sections = $page->getSections();

// ...
```

### Caching, caching, caching!

> Each time you grab a page - you do the real API calls!

Use caching to improve your application speed and reduce API load:

```php
$html = Cache::remember($key, $minutes, function () {
    return (new Wikipedia)->page('Donald Trump')->getBody();
});
```

## License

The MIT License. Please see [License File](LICENSE) for more information.

[<img src="https://user-images.githubusercontent.com/1286821/43086829-ff7c006e-8ea6-11e8-8b03-ecf97ca95b2e.png" alt="Support on Patreon" width="125" />](https://patreon.com/dmitryivanov)
