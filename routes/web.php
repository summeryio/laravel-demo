<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', [
    'as' => 'help',
    'uses' => 'StaticPagesController@help'
]);
Route::get('/about', 'StaticPagesController@about')->name('about');


// /signup signup  无区别
Route::get('/signup', 'UsersController@create')->name('signup');
Route::resource('users', 'UsersController');



Route::get('login', 'SessionController@create')->name('login');
Route::post('login', 'SessionController@store')->name('login');
Route::delete('logout', 'SessionController@destroy')->name('logout');



Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');