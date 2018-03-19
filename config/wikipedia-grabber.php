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

];
