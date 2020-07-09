<?php


Route::group([ 'middleware' => 'api'], function ($router) {

    // Route::post('login', 'AuthController@login')->name('login');
    // Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');



});


Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {

      //transactions
      Route::post('credit/account', 'TransactionController@store');
      //fetch all my transactions
      Route::get('/transactions','TransactionController@index');
});
