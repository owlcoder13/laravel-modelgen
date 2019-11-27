<?php

namespace Owlcoder\Providers;

use Illuminate\Support\ServiceProvider;
use Owlcoder\Console\Commands\GenerateModels;

class ModelGenProvider extends ServiceProvider
{
    function register()
    {
        parent::register();

        $this->commands([
            GenerateModels::class,
        ]);
    }
}
