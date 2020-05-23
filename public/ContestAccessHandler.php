<?php 
declare(strict_types=1);

header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Content-type: application/json");

/*
include "CurlRequest.php";
include "LoginHandler.php";
include "index.php";
include "SubmissionHandler.php";

*/

function getContestList($req,$res,$args){
	if(checkLoggedIn()){
		$url = "https://api.codechef.com/contests?fields=code,name&status=past&offset=1&limit=30";
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']['content']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getContestDetails($req,$res,$args){
	$contestCode=$args['contestcode'];
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/contests/'.$contestCode.'?fields=code,name,problemsList';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']['content']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getProblemDetails($req,$res,$args){
	$contestCode=$args['contestcode'];
	$problemCode= $args['problemcode'];
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/contests/'.$contestCode.'/problems/'.$problemCode.'?fields=problemName,body';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']['content']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}

function getRankList($req,$res,$args){
	$contestCode=$args['contestcode'];
	$offset = 1;
	if(isset($_GET['offset'])){
		$offset = $_GET['offset'];
	}
	if(checkLoggedIn()){
		$url = 'https://api.codechef.com/rankings/'.$contestCode.'?fields=rank,username,totalScore&offset='.$offset.'&sortBy=rank';
		$headers[] ='Authorization: Bearer ' . $_SESSION['access_token'];
		$response = CurlRequestMaker::getInstance()->make_curl_request($url,false,$headers);
		$data = json_decode($response,true);
		$res->getBody()->write(json_encode($data['result']['data']));
	}else{
		$res->getBody()->write(getErrorMessage());
	}
	return $res;
}
