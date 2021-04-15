<?php

/**
 * Wheather SMS API class to send SMS
 * @Author: Pradeep Kumar
 * email: mca.pradeeppai@gmail.com
 */
class SMSApi{
    private static $instance = null;
    const SMS_API_KEY = '5f9138288b71de3617a87cd3';
    const SMS_API_SECRET = 'CKwG8KXtiO';
    const API_ACCESS_URL = 'https://auth.routee.net/oauth/token';
    const API_SEND_SMS_URL = 'https://connect.routee.net/sms';
    const API_TIMEOUTS = 30;
    const API_MAXREDIRS = 10;
    //used to store client token after successful authentication
    static $client_token;

    //making constructor private means here to follow singleton pattern
    private function __construct(){
        self::register();
    }

    public static function register(){
        $curl = curl_init();
        $authorizationHeaderString = strtr("authorization: Basic {TOKEN}", ['{TOKEN}'=> self::generateToken()]);
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::API_ACCESS_URL ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => self::API_MAXREDIRS,
            CURLOPT_TIMEOUT => self::API_TIMEOUTS,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => array(
                $authorizationHeaderString,
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $msg_response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if(!$err){
            $resp = json_decode($msg_response);
            self::$client_token = $resp->access_token;
        }else{
            throw new \Exception('ERROR IN CALLING ROUTEE API');
        }
    }

    public static function send($number_with_country_code, $message){
        if(!empty(self::$client_token)){
            if($number_with_country_code != ''){
                $curl = curl_init();

                $body = [
                    'body' => $message,
                    'to' => $number_with_country_code,
                    'from' => 'amdTelecom',
                    ////set this callback when you want to update at your server
                    // 'callback' => [
                    //     'strategy' => 'OnCompletion',
                    //     'url' => 'http://www.yourserver.com/message'
                    // ]
                ];

                curl_setopt_array($curl, array(
                    CURLOPT_URL => self::API_SEND_SMS_URL ,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => self::API_MAXREDIRS,
                    CURLOPT_TIMEOUT => self::API_TIMEOUTS,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($body),
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer ".self::$client_token,
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    throw new \Exception($err);
                } else {
                    return $response;
                }
            }
        }
        throw new \Exception('INVALID_SMS_DATA');
    }


    /**
     * generateToken
     * input: Null
     * output : token
     */
    public static function generateToken(){
        return base64_encode(self::SMS_API_KEY.':'.self::SMS_API_SECRET);
    }


    //get single instance
    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self; 

        }
        return self::$instance;
    }

}