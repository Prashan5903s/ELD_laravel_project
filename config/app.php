<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
     */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
     */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
     */

    'debug' => (bool) env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
     */

    'url' => env('APP_URL', 'https://eld.apnatelelink.us/uat'),

    'asset_url' => env('ASSET_URL'),

    'Map_key' => 'AIzaSyBB7W_XXb6ez5hA1jpwYUr4zWS8JF_fJ7A',

    'weight_hos_violation' => 10,

    'weight_speeding' => 5,

    'weight_harsh_driving' => 3,

    'weight_hard_braking' => 10,

    'weight_hard_accel' => 5,

    'weight_hard_stop' => 10,

    'weight_hard_turn' => 5,

    'ELD_OUTPUT_FILE_TOKEN' => "dK7wLz19qXePvM3rTYc8JFAbnQ4GHvUBzELkSoIaRtmx2pD5yN",

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
     */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
     */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
     */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
     */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
     */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
     */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
     */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
     */

    'aliases' => Facade::defaultAliases()->merge([
        'GoogleTranslate' => Stichoza\GoogleTranslate\GoogleTranslate::class,
        // 'ExampleClass' => App\Example\ExampleClass::class,
    ])->toArray(),

    'TH' => [
        '1' => '500MB',
        '2' => '1GB',
        '3' => '2GB',
        '4' => '3GB',
        '5' => '4GB',
        '6' => '5GB',
        '7' => '6GB',
    ],

    'durations' => [
        '1' => '1 Month',
        '2' => '2 Months',
        '3' => 'Quarterly',
        '6' => '6 Months',
        '9' => '9 Months',
        '12' => '1 Year',
    ],

    'eld_web' => 'http://localhost:3000',

    'eld_mail' => 'support@uateld.com',

    'eld__comp_name' => 'UAT ELD',

    'eld_mobileNo' => "+1 80 XXXX XXXX XXX",

    'HOS' => [
        '1' => 'ELD Extempt',
        '2' => 'ELD Personal Conveynance (PC)',
        '3' => 'ELD Yard Moves (YM)',
        '4' => 'Waiting Time (WT)',
    ],

    'EDSH' => [
        '1' => '12AM (Midnight)',
        '2' => '12PM (Noon)',
    ],

    'UE' => [
        '1' => '16 Hour Short-Haul 395.1(o)',
        // '2' => 'Adverse Driving (USA) 395.1(b)(1)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Location Address Type
    |--------------------------------------------------------------------------
    |
    | This is used for provide the location address type
    | However, feel free to register as many as you wish.
    |
     */

    'address_types' => [
        '1' => 'Normal Geofence',
        '2' => 'Yard',
        '3' => 'Risk Zone',
        '4' => 'Alerts Only',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vehicle Year
    |--------------------------------------------------------------------------
    |
    | This is used for provide the vehicle year
    | However, feel free to register as many as you wish.
    |
     */

    'vehicle_year' => '1990',

];
