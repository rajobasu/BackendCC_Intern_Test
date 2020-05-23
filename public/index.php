<?php 
declare(strict_types=1);
session_start();
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Content-type: application/json");
//header("Access-Control-Allow-Headers: Content-Type, origin");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

include "CurlRequest.php";
include "LoginHandler.php";
include "ContestAccessHandler.php";

require __DIR__ . '/../vendor/autoload.php';

$CLIENT_ID = "2772597d9ced75b5d8fc0e0a569e27c2";
$CLIENT_SECRET = "537bfd068d4bdcf6e54eafe1793ce083";
$REDIRECT_URI = 'http://localhost:8080/api/login/keydetails';

/**
 * 
 * This was mostly used for testing, still kept.
 */
function isLoggedIn(){
	if(isset($_SESSION['access_token']))return "y";
	return "x";
}


function logincheckr($req, $res, $args){
	$res->getBody()->write(isLoggedIn());
	return $res;
}


function getUserInfo($req,$res,$args){
	if(checkLoggedIn()){
		
		$url = "https://api.codechef.com/users/me";
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']['content']));
		return $res;
	}else{
		$res->getBody()->write(getErrorMessage());
		return $res;
	}	
}


function validateLogin($callback){
	return function($req, $res, $args) use ($callback){
     	if(checkLoggedIn()){
			 refreshToken();
			return $callback($req, $res, $args);
		}else{
			$res->getBody()->write(getErrorMessage());
			return $res;
		}
	};
}



$app = AppFactory::create();
// Define app routes
$app->addErrorMiddleware(true,true,false);
// define the routes;
$app->get('/api/login', attemptOAuthLogin);
$app->get('/api/login/keydetails', getAccessTokens);
$app->get('/api/logout',logout);
$app->get('/api/loggedinstatus', logincheckr);
$app->get('/api/userinfo', getUserInfo );
$app->get('/api/contestlist',getContestList);
$app->get('/api/contestpage/{contestcode}', getContestDetails);
$app->get('/api/contestpage/{contestcode}/problems/{problemcode}', getProblemDetails);
$app->get('/api/ranklist/{contestcode}',getRankList);
$app->get('/api/submissions/{contestCode}/user/{username}', getSubmissionsByUser);
$app->get('/api/submissions/{problemCode}', getSubmissionsByProblem);
$app->get('/api/languages', getSupportedLanguages);
$app->get('/api/getsubmitstatus/{link}', getSubmissionStatus);

$app->post('/api/submit', submitCode);


// Run app
$app->run();

?>