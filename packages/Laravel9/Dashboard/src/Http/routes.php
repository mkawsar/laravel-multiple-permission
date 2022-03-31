<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1'], function () {
    Route::group(['namespace' => 'Laravel9\Dashboard\Http\Controllers', 'prefix' => 'dashboard', 'middleware' => ['jwt.verify', 'jwt.auth', 'role:admin']], function () {
        Route::get('index', 'DashboardController@index');
    });
});
