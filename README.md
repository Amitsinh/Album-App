Album
=====

<p>Album App is a free service that allows you to move your facebook profile's Album to Picasa.It also allows you to  download all your photos into a single Zip archive!
 <br><br>Try it! It's free.</p>
 
 Overwrite directory-level configuration using .htaccess. Every Request first send to dispatch.php and as per request particular class’s method is called. (Used Custom Framework)  
<b>.htaccess</b>
<IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteCond %{REQUEST_URI} !dispatch\.php$
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteRule .* dispatch.php [QSA,PT]
</IfModule>

<br>

<b>dispatch.php<b>
<br>
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
require_once('config.php');
	require_once 'Db/Db.php';

	require_once($apiArray[$className]);
	$objClass = new $className;
	if((int)method_exists($objClass, $methodName)){
			$objClass->$methodName();
	}
       else{
		echo "<h1>Default method not found!";
	}
?>
