<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\CorsMiddleware::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        // \Fruitcake\Cors\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            // \App\Http\Middleware\ForceHttps::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CheckPermission::class,
        ],

        'api' => [
            // Uncomment the following middleware if you're using Sanctum for API authentication
            'throttle:300,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'mobileAPI' => [
            // Uncomment the following middleware if you're using Sanctum for API authentication
            'throttle:300,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api-acess' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ],

        'api-access-mobile' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ]


    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'mobileAPI' => \App\Http\Middleware\MobileAPIMiddleware::class,

        // Add your custom route middlewares here

        'SA' => \App\Http\Middleware\SACheck::class,

        'WC' => \App\Http\Middleware\WSCheck::class,

        'TR' => \App\Http\Middleware\TRCheck::class,

        'DR' => \App\Http\Middleware\DriverCheck::class,

        'RS' => \App\Http\Middleware\RSCheck::class,

        'EC' => \App\Http\Middleware\CCCheck::class,

        'ecCheck' => \App\Http\Middleware\CompanyUserChange::class,

        'DrCheck' => \App\Http\Middleware\DrCheckAPI::class,

        'DrCheckMobile' => \App\Http\Middleware\DrCheckMobileAPI::class,

        'trCheck' => \App\Http\Middleware\TRChange::class,

        'ECAPI' => \App\Http\Middleware\ECChecKAPI::class,

        'TRAPI' => \App\Http\Middleware\TRChecKAPI::class,

        'permission' => \App\Http\Middleware\CheckCustomPermission::class,

        "APILogCheck" => \App\Http\Middleware\AuthAPI::class

    ];


}