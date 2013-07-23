<?php
    require 'lib/facebook-php-sdk-master/src/facebook.php';
    require 'picasa.php';
    require 'PHPMailer/class.phpmailer.php';


	class home{

        function display()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));

            $user = $facebook->getUser();

            if ($user)
            {
                //Check User session is set
                // if not than fetch user info and check user exists. then save user info into session
                //otherwise insert new record and set session
                if(!isset($_SESSION['user_name']))
                {
			$userinfo = $facebook->api('/me?fields=email,username,name&access_token=' .$facebook->getAccessToken());
			
                    $_SESSION['email']=$userinfo['email'];
                    $db = new Database();
                    $db->connect();
                    // check user exists if yes then set use_name & picasa_token into session

                    $checkUser = $db->fetch_all_array("SELECT
										*
								FROM
										user_master
								WHERE
										facebook_id = ".$userinfo['id']);

                    if(!$checkUser)
                    {
                        $data3 = array();
                        $data3['facebook_id']=$userinfo['id'];
                        $data3['username']=$userinfo['username'];
                        $data3['name']=$userinfo['name'];
                        $id=$db->query_insert("user_master",$data3);
                        $_SESSION['user_name']=$userinfo['name'];
                        $_SESSION['user_id']=$id;

                    }else{
                        $_SESSION['user_name']=$checkUser[0]['name'];
                        $_SESSION['sessionToken']=$checkUser[0]['picasa_token'];
                        $_SESSION['user_id']=$checkUser[0]['id'];

                    }
                }

                $params = array( 'next' => next_logout);
                 $logoutUrl = $facebook->getLogoutUrl($params);
            }
            else
            {
                $params = array( 'scope' => 'user_photos,user_status,email');
                $loginUrl = $facebook->getLoginUrl($params);
            }
            include 'Templete/index.php';
        }

        //Load Album after user logged in successfully.
        function viewalbum()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));
            $photos = $facebook->api('/'.$_REQUEST['id'].'?fields=photos.fields(source)&access_token='.$facebook->getAccessToken());
            for($i=0;$i<count($photos['photos']['data']);$i++)
            {
                echo "<a href='".$photos['photos']['data'][$i]['source']."'class='fancybox' data-fancybox-group='gallery' title='".$photos['photos']['data'][$i]['id']."'></a>";
            }
        }

        // Sent Email With Zip link

        function email()
        {
            error_reporting(E_ERROR);

            $message = '<html>

                            <body>
                                <div style="border:1px solid #ccc;">
                                    <div style="padding:20px;text-size:16px;color:#fff;background:#2BA6CB;border-bottom:1px solid #ccc;">Album App</div>
                                    <div style="padding:20px; color:#666666;">
                                        <p><b>Hello '.$_SESSION['user_name'].',</b></p><br />
                                        <p>Thank you for using our service.</p>
					      <p>To Download Your zipped album,Click on <a href=http://50.112.48.91/'.$_SESSION['fileName'].'>http://50.112.48.91/'.$_SESSION['fileName'].'</a></p><br/>
                                        <p>Album App <br>Director </p>
                                    <div>
                                </div>
                            </body>
                            </html>';

              $subject='Album Zip Link';
              $to =$_SESSION['email'];

		$mail = new PHPMailer;

		$mail->IsSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';  // Specify main and backup server
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'EmailId';                            // SMTP username
		$mail->Password = 'Password';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
		
		$mail->From = 'Notification@AlbumApp.com';
		$mail->FromName = 'AlbumApp Notification';
		$mail->AddAddress($_SESSION['email'], $_SESSION['user_name']);  // Add a recipient
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->IsHTML(true);                                  // Set email format to HTML
		$mail->Subject = 'Zip Link';
		$mail->Body    = $message;
		

		if(!$mail->Send()) {
		    echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		    exit;
		} 
           
            
            echo $_SESSION['email'];
        }

        //Logout from Facebook & Album App
        function logout()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));
            $facebook->destroySession();
            session_destroy();
            header("Location:/home");

        }

}
