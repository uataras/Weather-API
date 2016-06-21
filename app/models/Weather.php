<?php

    class Weather extends Eloquent {

        protected $table = 'data_weather';

        protected $fillable = [
          'longitude',
          'latitude',
          'weather_id',
          'weather_icon',
          'weather_description',
          'temperature',
          'pressure',
          'humidity',
          'temp_min',
          'temp_max',
          'wind_speed',
          'wind_degrees',
          'cloudiness',
          'date',
          'country',
          'city_id',
          'city_name',
        ];
    }
