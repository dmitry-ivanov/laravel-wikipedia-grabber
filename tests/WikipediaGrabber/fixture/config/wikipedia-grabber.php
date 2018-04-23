<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Output Format
    |--------------------------------------------------------------------------
    |
    | Specify here output format for the grabbed Wikipedia or MediaWiki pages.
    | It defines html decoration for headings, sections and other elements.
    | Default format is plain, which is not using any of css frameworks.
    |
    | Supported: "plain", "bulma".
    |
    */

    'format' => 'bulma',

    /*
    |--------------------------------------------------------------------------
    | Images Grabbing
    |--------------------------------------------------------------------------
    |
    | By default, pages are grabbed with images. You can change this behavior.
    | If you're interested only in the page text, then disable this setting.
    | Images are making page prettier, but grabbing takes some more time.
    |
    */

    'images' => true,

    /*
    |--------------------------------------------------------------------------
    | Image Size
    |--------------------------------------------------------------------------
    |
    | Here you can specify the size of image thumbnails on the grabbed page.
    | According to proportions, it would be used for the width or height.
    | In most cases, default value is fine. Change it, if you need it.
    |
    */

    'image_size' => 300,

    /*
    |--------------------------------------------------------------------------
    | User-Agent
    |--------------------------------------------------------------------------
    |
    | MediaWiki API asks us to identify our client by specifying unique User-Agent.
    | By default, the header will be composed from your application name and url.
    | It's recommended to override it and specify some of your contacts also.
    |
    | Default: "Application Name (http://example.com)"
    | Recommended: "Application Name (http://example.com; foo@example.com)"
    |
    | @see https://www.mediawiki.org/wiki/API:Main_page#Identifying_your_client
    |
    */

    'user_agent' => 'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)',

    /*
    |--------------------------------------------------------------------------
    | Boring Sections
    |--------------------------------------------------------------------------
    |
    | Grabbed version of the page is light and clean, it has no external links.
    | Without links, some of the sections became totally useless and boring.
    | Here is the list of such boring sections. All of them are skipped.
    |
    */

    'boring_sections' => [
        'en' => [
            'Boring section 1',
            'Boring section 2',
            'Boring section 3',
        ],
        'ru' => [
            'Скучная секция 1',
            'Скучная секция 2',
            'Скучная секция 3',
        ],
    ],

];
