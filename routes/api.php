<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'api'], function () {
    Route::post('login', 'APIController@login');
});


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('submitAnsRegularQuiz', array('as' => 'submitAnsRegularQuiz', 'uses' => 'QuizController@submitAnsRegularQuiz'));
});