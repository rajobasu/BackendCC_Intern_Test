<?php 
declare(strict_types=1);

//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Content-type: application/json");
//header("Access-Control-Allow-Headers: Content-Type, origin");
/* 
include "CurlRequest.php";
include "LoginHandler.php";
include "ContestAccessHandler.php";

*/


function getSubmissionsByUser($req,$res,$args){
	$contestCode = $args['contestCode'];
	$username = $args['username'];
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/submissions/?username='.$username.'&contestCode='.$contestCode.'&fields=date,problemCode,language,result,time,memory&limit=20';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getSubmissionsByProblem($req,$res,$args){
	$problemCode = $args['problemCode'];
	
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/submissions/?problemCode='.$problemCode.'&fields=date,username,language,time,memory&result=AC&limit=20';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getSupportedLanguages($req,$res,$args){
	
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/language?limit=100';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

/**
 * This is just to submit the code. Checking submission status is handled by another function.
 */
function submitCode($req, $res, $args){
	//echo "hhaaha";
	//  $res->getBody()->write(var_dump($_POST));
	// return $res;
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/ide/run';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		
		$params = array(
			"sourceCode"=>$_REQUEST['sourceCode'],
			"language"=>$_REQUEST['language'],
			"input"=>$_REQUEST['testCase'],
		);
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,$params,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getSubmissionStatus($req,$res,$args){
	if(checkLoggedIn()){

		$url = 'https://api.codechef.com/ide/status?link='.$args['link'];
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}


