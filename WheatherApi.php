<?php

/**
 * Wheather API class to interect with the OpenWheather API
 * @Author: Pradeep Kumar
 * email: mca.pradeeppai@gmail.com
 */

class WheatherApi{
    const API_KEY = 'b385aa7d4e568152288b3c9f5c2458a5';
    //to collect temprature in Celsius we use units=metric
    const API_END_POINT = 'https://api.openweathermap.org/data/2.5/weather?q={CITY_NAME}&units=metric&appid={API_KEY}';
    
    /**
     * inputs: $city 
     * outputs: $temprature
     * description: we will get temprature in Celsius
     */
    public function getCurrentWheather($city){
        $temprature = 'INVALID_DATA';
        if($city != ''){
            $apiURL = strtr(self::API_END_POINT, [
                '{CITY_NAME}' => $city,
                '{API_KEY}' => self::API_KEY
            ]);
            
            //fetching data through curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);//collect error if any
            curl_close($ch); // Close the connection
            
            if(!$err){
                $result = json_decode($response);
                if($result)
                    $temprature = $result->main->temp;
            }  
        }
        return $temprature;
    }
}