<?php

use App\Http\Middleware\CheckActivationCode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Checkisadmin;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
$middleware->alias([

   'activation' => CheckActivationCode::class,
   'admin'=>Checkisadmin::class
    ]);
 

  
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
