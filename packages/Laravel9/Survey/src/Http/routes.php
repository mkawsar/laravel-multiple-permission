<?php

use Illuminate\Support\Facades\Route;
use Laravel9\Survey\Http\Controllers\SurveyController;

Route::group(['prefix' => 'api/v1'], function () {
    Route::group(['middleware' => ['jwt.verify', 'jwt.auth', 'role:admin']], function () {
        Route::resource('survey', SurveyController::class);
    });
});
