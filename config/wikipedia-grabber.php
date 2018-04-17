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

    'format' => 'plain',

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
    | User-Agent
    |--------------------------------------------------------------------------
    |
    | MediaWiki API asks us to identify our client by specifying unique User-Agent.
    | By default, the header will be composed from your application name and url.
    | It's recommended to override it and specify some of your contacts also.
    |
    | Default: "Application Name (http://example.com)" (if null set)
    | Recommended: "Application Name (http://example.com; foo@example.com)"
    |
    | @see https://www.mediawiki.org/wiki/API:Main_page#Identifying_your_client
    |
    */

    'user_agent' => null,

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
            'Bibliography',
            'Bibliography and further reading',
            'External links',
            'External references',
            'Footnotes',
            'Further reading',
            'Further reading/listening/viewing',
            'Literature',
            'Notes',
            'Notes and references',
            'Quotations',
            'References',
            'References and notes',
            'References and sources',
            'Secondary literature',
            'See also',
            'Source',
            'Sources',
        ],
        'ru' => [
            'Библиография',
            'Другие ссылки',
            'Использованные источники',
            'Исследования',
            'Исследования и научно-популярная литература',
            'Источники',
            'Источники и библиография',
            'Источники и литература',
            'Источники и примечания',
            'Источники и ссылки',
            'Комментарии',
            'Комментарии и цитаты',
            'Литература',
            'Примечания',
            'Русская библиография',
            'Сноски и источники',
            'Ссылки',
            'Ссылки и источники',
            'Ссылки и литература',
            'Смотрите также',
            'См.также',
            'См. также',
            'Сноски',
            'Тематические ссылки',
        ],
    ],

];
