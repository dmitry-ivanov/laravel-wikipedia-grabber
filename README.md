![Wikipedia/MediaWiki Grabber for Laravel](art/1380x575-optimized.jpg)

# Laravel Wikipedia Grabber

[<img src="https://user-images.githubusercontent.com/1286821/181085373-12eee197-187a-4438-90fe-571ac6d68900.png" alt="Buy me a coffee" width="200" />](https://buymeacoffee.com/dmitry.ivanov)

[![StyleCI](https://github.styleci.io/repos/117998599/shield?branch=12.x&style=flat)](https://github.styleci.io/repos/117998599?branch=12.x)
[![Build Status](https://img.shields.io/github/actions/workflow/status/dmitry-ivanov/laravel-wikipedia-grabber/tests.yml?branch=12.x)](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/actions?query=workflow%3Atests+branch%3A12.x)
[![Coverage Status](https://img.shields.io/codecov/c/github/dmitry-ivanov/laravel-wikipedia-grabber/12.x)](https://app.codecov.io/gh/dmitry-ivanov/laravel-wikipedia-grabber/tree/12.x)

![Packagist Version](https://img.shields.io/packagist/v/illuminated/wikipedia-grabber)
![Packagist Stars](https://img.shields.io/packagist/stars/illuminated/wikipedia-grabber)
![Packagist Downloads](https://img.shields.io/packagist/dt/illuminated/wikipedia-grabber)
![Packagist License](https://img.shields.io/packagist/l/illuminated/wikipedia-grabber)

Wikipedia/MediaWiki Grabber for Laravel.

| Laravel | Wikipedia Grabber                                                            |
|---------|------------------------------------------------------------------------------|
| 12.x    | [12.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/12.x) |
| 11.x    | [11.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/11.x) |
| 10.x    | [10.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/10.x) |
| 9.x     | [9.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/9.x)   |
| 8.x     | [8.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/8.x)   |
| 7.x     | [7.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/7.x)   |
| 6.x     | [6.x](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/6.x)   |
| 5.8.*   | [5.8.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.8) |
| 5.7.*   | [5.7.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.7) |
| 5.6.*   | [5.6.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.6) |
| 5.5.*   | [5.5.*](https://github.com/dmitry-ivanov/laravel-wikipedia-grabber/tree/5.5) |

![Laravel Wikipedia Grabber - Demo](doc/img/demo.gif)

## Table of contents

- [Usage](#usage)
- [Output formats](#output-formats)
- [Available methods](#available-methods)
- [Advanced](#advanced)
  - [MediaWiki](#mediawiki)
  - [Modify the grabbed page](#modify-the-grabbed-page)
- [Sponsors](#sponsors)
- [License](#license)

## Usage

1. Install the package via Composer:

    ```shell script
    composer require "illuminated/wikipedia-grabber:^12.0"
    ```

2. Publish the config:

    ```shell script
    php artisan vendor:publish --provider="Illuminated\Wikipedia\WikipediaGrabberServiceProvider"
    ```

3. Grab a full page or preview:

    ```php
    use Wikipedia;

    echo (new Wikipedia)->page('Michael Jackson');
    echo (new Wikipedia)->preview('Michael Jackson');

    // Or

    echo (new Wikipedia)->randomPage();
    echo (new Wikipedia)->randomPreview();
    ```

## Output formats

Here's the list of supported output formats:

- `plain` (default)
- `bootstrap`
- `bulma`

Change the format in your config file, or specify it explicitly:

```php
echo (new Wikipedia)->page('Michael Jackson')->bulma();
```

## Available methods

When you call the `page()` or `preview()` method, you'll get an instance of the proper object.

There are numerous methods available on these objects, for example:

```php
$page = (new Wikipedia)->page('Michael Jackson');

$page->isSuccess();         // true
$page->isMissing();         // false
$page->isInvalid();         // false
$page->isDisambiguation();  // false

echo $page->getId();        // 14995351
echo $page->getTitle();     // "Michael Jackson"
echo $page->getBody();      // Same as `echo $page;`
```

## Advanced

### MediaWiki

Wikipedia uses the [MediaWiki API](https://mediawiki.org/wiki/API:Main_page) under the hood.

Thus, you can grab pages from any MediaWiki website:

```php
use MediaWiki;

echo (new MediaWiki($url))->page('Michael Jackson');
```

### Modify the grabbed page

Sometimes it might be useful to append additional sections to the grabbed page:

```php
$page = (new Wikipedia)->page('Michael Jackson');

$page->append('Interesting Facts', 'He had two pet llamas on his ranch called Lola and Louis.');
```

Alternatively, you can get the sections collection and change it as needed:

```php
$page = (new Wikipedia)->page('Michael Jackson');

$sections = $page->getSections();
$sections->push(
    new Section('Interesting Facts', 'He had two pet llamas on his ranch called Lola and Louis.', $level = 2)
);
```

## Sponsors

[![Laravel Idea](art/sponsor-laravel-idea.png)](https://laravel-idea.com)<br>
[![Material Theme UI Plugin](art/sponsor-material-theme.png)](https://material-theme.com)<br>

## License

Laravel Wikipedia Grabber is open-sourced software licensed under the [MIT license](LICENSE.md).

[<img src="https://user-images.githubusercontent.com/1286821/181085373-12eee197-187a-4438-90fe-571ac6d68900.png" alt="Buy me a coffee" width="200" />](https://buymeacoffee.com/dmitry.ivanov)&nbsp;
