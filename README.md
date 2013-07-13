Album
=====

<p>Album App is a free service that allows you to move your facebook profile's Album to Picasa.It also allows you to  download all your photos into a single Zip archive!
<br><br>Try it! It's free.</p>
<hr>
 Overwrite directory-level configuration using .htaccess. Every Request first redirect to dispatch.php and as per request particular class’s method is called. (Used Custom Framework)  
<h4>.htaccess</h4>
<pre>
<IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteCond %{REQUEST_URI} !dispatch\.php$
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteRule .* dispatch.php [QSA,PT]
</IfModule>
</pre>

<h4>dispatch.php<h4>

<pre>

  	.........................
  	.........................
	require_once('config.php');
	require_once 'Db/Db.php';

	require_once($apiArray[$className]);
	$objClass = new $className;
	if((int)method_exists($objClass, $methodName)){
			$objClass->$methodName();
	}
       else{
		echo "<b>Default method not found!</b>";
	}

</pre>
<h4>Library Used <h4>

<pre>
	1. facebook-php-sdk-master
	2. foundation-4.2.2
  	3. ZendGdata-1.12.3 
	4. Ajaxloder
	5. touchSwipe
	6. fancybox	
</pre>
