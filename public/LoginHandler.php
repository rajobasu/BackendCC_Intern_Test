<?php
declare(strict_types = 1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



function checkLoggedIn(){
	if(isset($_SESSION['access_token']))return true;
	else return false;
}


/**
 * This is called when the user tries to make a Login
 */

function attemptOAuthLogin(Request $request, Response $response, $args){
    if(isset($_SESSION['access_token'])){
        //echo "already Logged In";
        header('Location: http://localhost:3000');
        die();
        return $response;
    }

	$CC_URL = "https://api.codechef.com/oauth/authorize";
	
	$params = array('response_type'=>'code', 'client_id'=> $GLOBALS['CLIENT_ID'], 								'redirect_uri'=> $GLOBALS['REDIRECT_URI'], 'state'=> 'xyz');
    
    
	$reqMaker = CurlRequestMaker::getInstance();
    $reqMaker->makeRedirectRequest($CC_URL,$params);
        
    return $response;
}
/**
 * 
 * This is called by the CC API as the redirect URL. Not by frontend.
 */
function getAccessTokens(Request $request, Response $response, $args) {
	$URL_AUTH_ENDPOINT = "https://api.codechef.com/oauth/token";
	
	$params = array(
                "grant_type"=>"authorization_code" , 
                "code"=> $_GET['code'], 
                "client_id"=>$GLOBALS['CLIENT_ID'],
                "client_secret"=>$GLOBALS['CLIENT_SECRET'], 
                "redirect_uri"=>$GLOBALS['REDIRECT_URI']);
                
	$rmObjec = CurlRequestMaker::getInstance();
	$dataGot = $rmObjec->makePOSTRequest($URL_AUTH_ENDPOINT,$params);
    $jsonData = json_decode($dataGot,true);
    $accessToken = $jsonData['result']['data']['access_token'];
    $refreshToken = $jsonData['result']['data']['refresh_token'];
    $_SESSION['access_token'] = $accessToken;
    $_SESSION['refresh_token'] = $refreshToken;
    $_SESSION['time_'] = time();
    header('Location: http://localhost:3000');
    die();
    $response->getBody()->write($dataGot);
    return $response;
}
/**
 * Just Destroy the session.
 */
function logout(Request $request, Response $response, $args){
    session_destroy();
    return $response;
}

function generate_access_token_from_refresh_token(){
    $URL = 'https://api.codechef.com/oauth/token';

    $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $GLOBALS['refresh_token'], 'client_id' => $GLOBALS['CLIENT_ID'],
        'client_secret' => $GLOBALS['CLIENT_SECRET']);
    $response = json_decode(make_curl_request($URLs, $oauth_config), true);
    $result = $response['result']['data'];

    $_SESSION['access_token'] = $result['access_token'];
    $_SESSION['refresh_token'] = $result['refresh_token'];
    //$oauth_details['scope'] = $result['scope'];
    $_SESSION['time_'] = time();
    return $oauth_details;

}


function refreshToken(){
    if(!checkLoggedIn())return;
    if(time() > 3500 + $_SESSION['time_'])generate_access_token_from_refresh_token();
}


/**
 * If the user is not logged in, this message is sent. This allows the frontend to recognise that the used needs to login first. 
 */
function getErrorMessage(){
	$rawerrormessage = array("loginError" => "User has been logged out");
	$errormessage = json_encode($rawerrormessage);
	return $errormessage;
}

