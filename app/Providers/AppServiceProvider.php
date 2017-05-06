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
        /*\DB::listen(function ($query) {
            var_dump([
                $query->sql,
                $query->bindings,
                $query->time
            ]);
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Appzcoder\CrudGenerator\CrudGeneratorServiceProvider');

            //CRUD and Model generators
            $this->app->register('Way\Generators\GeneratorsServiceProvider');
            $this->app->register('Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider');
            $this->app->register('User11001\EloquentModelGenerator\EloquentModelGeneratorProvider');
        }
    }
}
