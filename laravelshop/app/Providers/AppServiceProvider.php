<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Controller;

class AppServiceProvider extends ServiceProvider
{

        //пробуем передать имя сайта во все вьюхи
    private function putVarsToView()
    {
        $controller = new Controller;
        $siteName = $controller -> getConfig('siteName');
        $controller = null;
        view()->composer('*',function($view) use ($siteName) {
            $view->with('siteName',$siteName);
        });

    }
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->putVarsToView();
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
