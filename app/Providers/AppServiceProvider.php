<?php
namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckOwner;

class AppServiceProvider extends ServiceProvider
{
    protected $defer=true;
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        
        // $this->app->singleton(CheckAdmin::class ,function(){
        //     return new CheckAdmin();
        // });
        // $this->app->singleton(CheckOwner::class ,function(){
        //     return new CheckOwner();
        // });
    }
}