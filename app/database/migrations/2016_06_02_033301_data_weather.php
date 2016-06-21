<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataWeather extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_weather', function(Blueprint $table){
			$table->increments('id');
			$table->double('latitude');
			$table->double('longitude');
			$table->integer('weather_id');
			$table->integer('weather_icon');
			$table->string('weather_description');
			$table->double('temperature');
			$table->double('pressure');
			$table->integer('humidity');
			$table->double('temp_min');
			$table->double('temp_max');
			$table->double('wind_speed');
			$table->double('wind_degrees');
			$table->integer('cloudiness');
			$table->integer('date');
			$table->string('country');
			$table->integer('city_id');
			$table->string('city_name');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_weather');
	}

}
