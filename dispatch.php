<?php 
	session_start();
   	error_reporting(E_ALL);
	
	$val = str_replace('','',$_SERVER['REDIRECT_URL']);
	$valArray = explode('/',$val);
	
       
	$apiArray = array();
	
	$apiArray[''] = 'Controller/home.php';
	$apiArray['home'] = 'Controller/home.php';
       $apiArray['picasa'] = 'Controller/picasa.php';


	$className = isset($apiArray[$valArray[1]]) && strlen($valArray[1]) > 0 ? $valArray[1]   : "home";
	$methodName = isset($valArray[2]) ? $valArray[2]   : "display";

//    Check for uses session
	if (!isset($_SESSION['user_id']) && $className!="home") {
		header('Location:/home');
		exit;
	}

if($valArray[0]!='Dump')
{

	require_once('config.php');
	require_once 'Db/Db.php';

	require_once($apiArray[$className]);
	$objClass = new $className;
	if((int)method_exists($objClass, $methodName)){
			$objClass->$methodName();
	}else{
		$methodName = "display";
	if((int)method_exists($objClass, $methodName)){
		$objClass->$methodName();
		}else{
			echo "<h1>Default method not found!";
		}
	}
}
	
?>
