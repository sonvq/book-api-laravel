<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 *	Models are bson encoded objects (mongoDB)
 */
Route::model('users', 'User');


Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/v1'], function()
{
    Route::post('register', 'AuthenticateController@register');
    Route::post('login', 'AuthenticateController@login');
    Route::get('token/refresh', 'AuthenticateController@refresh');
    
    Route::group(['middleware' => 'jwt.auth'], function() {
        Route::get('authenticate', 'AuthenticateController@getAuthenticatedUser');
        Route::resource('books', 'BookController');
        
        Route::resource('categories', 'CategoryController');
    });
        
	    
});