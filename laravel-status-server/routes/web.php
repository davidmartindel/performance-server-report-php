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


Route::group([
  "prefix"  =>  "statusserver"
], function(){
  Route::group(["namespace" =>  "StatusServer"], function()
  {
    Route::get("/", "StatusServerController@index")->name("StatusServer.index");
  });

});
