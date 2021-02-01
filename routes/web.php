<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware('auth:admin')->as('admin.')->namespace('App\Http\Controllers\Admin')
    ->group(function (){
        Route::group([
            'prefix'=>'/categories',

            'as'=>'categories.'

        ],function (){
            Route::get('/','CategoriesController@index')->name('index');
            Route::get('/create','CategoriesController@create')->name('create');
            Route::post('/store','CategoriesController@store')->name('store');
            Route::get('/{category}','CategoriesController@edit')->name('edit');
            Route::put('/{category}','CategoriesController@update')->name('update');
            Route::delete('/{category}','CategoriesController@destroy')->name('delete');
            Route::get('/{category}/products','CategoriesController@products')->name('products');
        });
        Route::get('products/trash','ProductsController@trash')->name('products.trash');
        //Route::delete('products/{id}/forceDelete','ProductsController@forceDelete')->name('products.forceDelete');
        Route::put('products/{id}/restore','ProductsController@restore')->name('products.restore');
        Route::resource('products','ProductsController');

    });








Auth::routes([
    'verify'=>true,
]);

Route::get('/home', function (){
    return view('welcome');
})->name('home');
