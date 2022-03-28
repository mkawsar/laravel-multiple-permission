<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1'], function () {
    Route::group(['namespace' => 'Laravel9\Auth\Http\Controllers'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::middleware(['jwt.verify', 'jwt.auth', 'role:admin'])->group(function () {
            Route::get('user/info', 'AuthController@getUser');
        });
    });
});
