<?php

namespace App\Providers;

use Illuminate\
    {
        Support\ServiceProvider,
        Support\Facades\DB
    };
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{


    // if (!file_exists('core/storage/installed') && !request()->is('install') && !request()->is('install/*')) {
    //     header("Location: install/");
    //     exit;
    // }

    public function boot()
    {
        Paginator::useBootstrap();
        view()->composer('*',function($settings){
            $settings->with('setting', DB::table('settings')->find(1));
            $settings->with('extra_settings', DB::table('extra_settings')->find(1));

            if (!session()->has('popup'))
            {
                view()->share('visit', 1);
            }
            session()->put('popup' , 1);
        });
    }
}

