<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Support\ServiceProvider;
use App\Models\Language;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Pagination\Paginator;
use App\Models\Hardware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        $lang = Language::where('is_active', 1)->get();

        view()->composer('transport.layout.navbar', function ($view) use ($lang) {
            $view->with('lang', $lang);
        });

        view()->composer('company.layout.left-slidebar', function ($view) use ($lang) {
            $id = Auth::user()->id;
            $userChang = User::where('user_type', 'TR')
                ->where('master_id', $id)
                ->get();

            $view->with('userChang', $userChang);
        });

        view()->composer('super-admin.layout.navbar', function ($view) use ($lang) {
            $user = Auth::user();
            $notifications = $user->notifications;
            $unreadNotificationsCount = $user->unreadNotifications->count();
            $user->unreadNotifications->markAsRead();

            $view->with('notifications', $notifications)
                ->with('unreadNotificationsCount', $unreadNotificationsCount);
        });

        view()->composer('super-admin.layout.left-slidebar', function ($view) use ($lang) {
            $hardware = Hardware::where('is_active', 1)->get();
            $view->with('hardware', $hardware);
        });
    }
}
