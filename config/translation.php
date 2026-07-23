<?php

use LukasCCB\AiTranslations\Translators\PrismTranslator;

return [

    /*
    |--------------------------------------------------------------------------
    | Source Language
    |--------------------------------------------------------------------------
    |
    | The source language of the project — always scanned and used as fallback.
    | All __() strings are registered under this language first.
    |
    */
    'source_language' => 'pt_BR',

    /*
    |--------------------------------------------------------------------------
    | Translation Batch Size
    |--------------------------------------------------------------------------
    |
    | Default number of strings to translate per AI batch run.
    |
    */
    'batch_size' => env('TRANSLATION_BATCH_SIZE', 500),

    /*
    |--------------------------------------------------------------------------
    | File Scanning
    |--------------------------------------------------------------------------
    |
    | Paths, file extensions, and exclusions for the translations:scan command.
    |
    */
    'scan' => [
        'paths' => [
            resource_path('views'),
            app_path(),
            base_path('lang'),
            resource_path('js'),
        ],

        'extensions' => ['php', 'blade.php', 'js', 'vue'],

        'exclude' => [
            base_path('vendor'),
            base_path('node_modules'),
            storage_path(),
            base_path('bootstrap/cache'),
            base_path('.git'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Column Scanning
    |--------------------------------------------------------------------------
    |
    | Database columns to scan for translatable strings.
    | These tables are READ ONLY — original tables are never modified.
    |
    | Simple format:  'table' => ['column1', 'column2']  — scans all rows
    | Filtered format: 'table' => [
    |     'column'        => 'col',
    |     'filter_by'     => 'key_col',
    |     'filter_values' => [...]
    | ]
    |
    */
    'database_scan' => [
        // Example:
        // 'roles' => ['description'],
        // 'settings' => [
        //     'column'        => 'value',
        //     'filter_by'     => 'key',
        //     'filter_values' => ['site_description', 'meta_title'],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Translator
    |--------------------------------------------------------------------------
    |
    | The class that implements TranslatorInterface for AI-powered translations.
    | The default PrismTranslator uses prism-php/prism.
    |
    | Set to null to disable AI translation commands.
    |
    */
    'translator' => PrismTranslator::class,

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Provider and model used by the default PrismTranslator.
    |
    */
    'ai' => [
        'provider' => env('TRANSLATION_AI_PROVIDER', 'openai'),
        'model' => env('TRANSLATION_AI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => env('TRANSLATION_AI_MAX_TOKENS', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Resolver
    |--------------------------------------------------------------------------
    |
    | How the SetLocale middleware resolves the user's preferred locale.
    | Override with a closure or class that receives the Request and returns
    | a locale string (or null for fallback).
    |
    | Default: reads from session('locale')
    |
    */
    'locale_resolver' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | TTL in seconds for cached translation maps. Set to 0 to disable caching.
    |
    */
    'cache_ttl' => env('TRANSLATION_CACHE_TTL', 3600),

];
