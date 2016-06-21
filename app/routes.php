<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::get('testWeather', 'WeatherController@getWeatherAPI');


Route::get('weather', 'WeatherController@addOrUpdateWeather');
Route::get('weather/registerSensor', 'WeatherController@checkCoordinates');
Route::get('weather/getDataByCoordinate', 'WeatherController@getDataByCoordinate');
Route::get('weather/updateByCoordinate', 'WeatherController@updateByCoordinate');








