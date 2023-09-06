<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\menu;
use Illuminate\Support\Facades\URL;

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

        view::composer("layouts.menu", function($view) {
            $menus = menu::getMenu(true);
            $view->with('menus', $menus);
        });
        
        if(env('FORCE_HTTPS',false)) { 
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                URL::forceScheme('http');
            } else if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                URL::forceScheme('https');
            }
        }

    }
}
