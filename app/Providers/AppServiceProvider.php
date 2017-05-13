<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        view()->composer('layouts.sidebar', function($view){
            
            $dir = realpath(getcwd().'/js/clientFiles');
            $json = json_decode(file_get_contents($dir.'/eventSources.json'));
            $clients = [];
            foreach($json->eventSources as $client){
                array_push($clients, $client);
            }

            $view->with(compact('clients'));

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
