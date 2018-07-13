# Laravel Wikipedia Grabber

[![StyleCI](https://styleci.io/repos/117998599/shield?branch=master&style=flat)](https://styleci.io/repos/117998599)
[![Build Status](https://travis-ci.org/dmitry-ivanov/laravel-wikipedia-grabber.svg?branch=master)](https://travis-ci.org/dmitry-ivanov/laravel-wikipedia-grabber)
[![Coverage Status](https://coveralls.io/repos/github/dmitry-ivanov/laravel-wikipedia-grabber/badge.svg?branch=master)](https://coveralls.io/github/dmitry-ivanov/laravel-wikipedia-grabber?branch=master)

[![Latest Stable Version](https://poser.pugx.org/illuminated/wikipedia-grabber/v/stable)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![Latest Unstable Version](https://poser.pugx.org/illuminated/wikipedia-grabber/v/unstable)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![Total Downloads](https://poser.pugx.org/illuminated/wikipedia-grabber/downloads)](https://packagist.org/packages/illuminated/wikipedia-grabber)
[![License](https://poser.pugx.org/illuminated/wikipedia-grabber/license)](https://packagist.org/packages/illuminated/wikipedia-grabber)

Provides convenient way to grab Wikipedia (or another MediaWiki) page.

| Laravel | Wikipedia Grabber                                                            |
| ------- | :--------------------------------------------------------------------------: |
| 5.5.*   | [5.5.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.5) |

## Table of contents

- [Usage](#usage)
- [Formats](#formats)
- [Languages](#languages)
- [Methods](#methods)
- [Advanced](#advanced)
  - [Config](#config)
  - [Preview](#preview)
  - [MediaWiki](#mediawiki)
  - [Use caching](#use-caching)
  - [Get page by id](#get-page-by-id)
  - [Add custom sections](#add-custom-sections)

## Usage

1. Install package through `composer`:

    ```shell
    composer require illuminated/wikipedia-grabber
    ```

2. Use `Wikipedia` class:

    ```php
    echo (new Wikipedia)->page('Donald Trump');
    ```

    Live demo would be added here soon.

## Formats

These formats are supported now:

- `plain` (default)
- `bulma` (see [Bulma](https://bulma.io))
- `bootstrap` (see [Bootstrap 3](https://getbootstrap.com/docs/3.3/), [Bootstrap 4](https://getbootstrap.com))

You can change format in your config (see [Configuration](#configuration)):

```php
'format' => 'bulma',
```

Or on the fly:

```php
echo (new Wikipedia)->page('Donald Trump')->bootstrap();
```

## Languages

> Only `en` and `ru` languages are supported now.

English is default language. However, you can change it:

```php
echo (new Wikipedia('ru'))->page('Donald Trump');
```

## Methods

Note that you have an object returned, so:

```php
$page = (new Wikipedia)->page('President Trump');

if ($page->isSuccess()) {
    echo $page->getId();    // 4848272
    echo $page->getTitle(); // Donald Trump
    echo $page;             // The same thing
    echo $page->getBody();  // The same thing
}
```

Here is an example of successfully grabbed page:

```php
$page = (new Wikipedia)->page('Donald Trump');

$page->isSuccess();         // true
$page->isMissing();         // false
$page->isInvalid();         // false
$page->isDisambiguation();  // false
```

And here is an example of successfully grabbed disambiguation page:

```php
$page = (new Wikipedia)->page('David Taylor');

$page->isSuccess();         // true
$page->isInvalid();         // false
$page->isMissing();         // false
$page->isDisambiguation();  // true
```

## Advanced

### Config
