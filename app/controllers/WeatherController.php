<?php

    use Cmfcmf\OpenWeatherMap;
    use Cmfcmf\OpenWeatherMap\Exception as OWMException;

    require 'vendor/autoload.php';

    class WeatherController extends Controller {


        public function getWeatherAPI($lat=null, $lon=null) {
            $lang = 'en';

            $units = 'metric';

            $owm = new OpenWeatherMap('3109f8aa729abedb9e46b0d71d1ca14b');

            $assocArray = null;
            if(isset($lat) && isset($lon) && is_numeric($lat) && is_numeric($lon) ) {
                $assocArray = array('lat' => $lat, 'lon' => $lon);
            }

            $weather = null;

            try {
                $weather = $owm->getRawDailyForecastData($assocArray, $units, $lang, '', 'json', 5);
            } catch(OWMException $e) {
                echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            } catch(\Exception $e) {
                echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            }

            if(is_null($weather)){
                return null;
            }

                return json_decode($weather);
        }


        public function addOrUpdateWeather($lat=null, $lon=null, $obj=null) {

            if(isset($lat) && isset($lon) && is_numeric($lat) && is_numeric($lon) ) {
                $weather = $this->getWeatherAPI($lat, $lon);

                if(is_null($weather)){
                    return null;
                }
                // create row
                foreach($weather->list as $item) {
                    $dbWeather = new Weather();
                    $dbWeather->longitude = $weather->city->coord->lon;
                    $dbWeather->latitude = $weather->city->coord->lat;
                    $dbWeather->weather_id = $item->weather[0]->id;
                    $dbWeather->weather_icon = $item->weather[0]->icon;
                    $dbWeather->weather_description = $item->weather[0]->description;
                    $dbWeather->temperature = $item->temp->day;
                    $dbWeather->pressure = $item->pressure;
                    $dbWeather->humidity = $item->humidity;
                    $dbWeather->temp_min = $item->temp->min;
                    $dbWeather->temp_max = $item->temp->max;
                    $dbWeather->wind_speed = $item->speed;
                    $dbWeather->wind_degrees = $item->deg;
                    $dbWeather->cloudiness = $item->clouds;
                    $dbWeather->date = $item->dt;
                    $dbWeather->country = $weather->city->country;
                    $dbWeather->city_id = $weather->city->id;
                    $dbWeather->city_name = $weather->city->name;
                    $dbWeather->save();
                }
                return array($weather->city->coord->lat, $weather->city->coord->lon);
            }
            elseif(!is_null($obj)) {
                // update row
                $weather = $this->getWeatherAPI($obj->latitude, $obj->longitude);
                if (is_null($weather)) {
                    return null;
                }

                $currentCityForecast = Weather::where('latitude','=',$obj->latitude)
                        ->where('longitude','=',$obj->longitude)
                        ->get();

                for ($i = 0; $i < count($currentCityForecast); $i++ ){
                    $dbModel = $currentCityForecast[$i];
                    $weatherModel = $weather->list[$i];

                    $dbModel->weather_id = $weatherModel->weather[0]->id;
                    $dbWeather->weather_icon = $item->weather[0]->icon;
                    $dbModel->weather_description = $weatherModel->weather[0]->description;
                    $dbModel->temperature = $weatherModel->temp->day;
                    $dbModel->pressure = $weatherModel->pressure;
                    $dbModel->humidity = $weatherModel->humidity;
                    $dbModel->temp_min = $weatherModel->temp->min;
                    $dbModel->temp_max = $weatherModel->temp->max;
                    $dbModel->wind_speed = $weatherModel->speed;
                    $dbModel->wind_degrees = $weatherModel->deg;
                    $dbModel->cloudiness = $weatherModel->clouds;
                    $dbModel->date = $weatherModel->dt;
                    $dbModel->save();
                }
            }
        }


        public function updateByCoordinate() {
            
            $tempArray = Weather::select('*')
                        ->distinct('latitude','longitude')
                        ->groupBy('latitude','longitude')
                        ->orderBy('id', 'asc')
                        ->get();

            foreach ($tempArray as $object){
                
                $this->addOrUpdateWeather(null,null,$object);

            }

        }


        public function checkCoordinates() {

            $lat = Input::get('latitude');
            $lon = Input::get('longitude');

            $type = 6371;
            $input_order = 'ASC';
            $radius = 30;

            $location_ids = Weather::select( DB::raw("*, 
                             ( ? * acos( cos( radians(?) ) * cos( radians( latitude ) )
                               * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) *
                               sin( radians( latitude ) ) ) ) AS distance"))
                ->having("distance", "<", $radius)
                ->orderBy("distance", $input_order)
                ->setBindings([$type, $lat, $lon, $lat])
                ->get("id")->toArray();

            if(is_null($location_ids) || empty($location_ids)) {
                $latLon = $this->addOrUpdateWeather($lat, $lon, null);
                return $latLon;
            }
            
            if(count($location_ids) > 1) {
                $min = $location_ids[0]['distance'];
                $latit = $location_ids[0]['latitude'];
                $longit = $location_ids[0]['longitude'];
                for ($i = 1; $i < count($location_ids); $i++) {
                    if ($min > $location_ids[$i]['distance']) {
                        $min = $location_ids[$i]['distance'];
                        $latit = $location_ids[$i]['latitude'];
                        $longit = $location_ids[$i]['longitude'];
                    }
                }
                return array('lat'=> $latit, 'lon' => $longit);
            }
            else{
                return array('lat'=> $location_ids[0]['latitude'], 'lon' => $location_ids[0]['longitude']);
            }
        }


        public function getDataByCoordinate() {
            
            $lat = Input::get('latitude');
            $lon = Input::get('longitude');

            $data = Weather::where('latitude','=',$lat)
                ->where('longitude','=',$lon)
                ->orderBy('date','asc')
                ->get()->toArray();
            return $data;
            
        }



    }




