<?php

//header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Headers: Content-Type, origin");

/**
 * This is a singleton class, to handle all GET/POST request between backend and Codechef Server
 * 
 */

class CurlRequestMaker{

    private static $INSTANCE;

    private final function __constructor(){

    }
    /**
     * This just redirects.
     */
    public function makeRedirectRequest($url, $params){
        $actualURL = $url . '?' . http_build_query($params);
        header('Location: ' . $actualURL);
        die();
       
    }
    /**
     * Not used as of yet. Instead, we just call make_curl_req(...) directly. 
     */
    public function makeGETRequest($url, $post){

    }

    /**
     * Nothing fancy. 
     */

    public function makePOSTRequest($url, $post){
        return $this->make_curl_request($url,$post);
    }

    /**
     * This code has been taken from the sample app provided in API Docs.
     */
    public function make_curl_request($url, $post = FALSE, $headers = array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }

        $headers[] = 'content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        return $response;
    }

    public static function getInstance() {
        if(!isset(self::$INSTANCE)) {
            self::$INSTANCE = new CurlRequestMaker();
        }
        return self::$INSTANCE;
    }
}
