<?php
    require 'lib/facebook-php-sdk-master/src/facebook.php';
    require 'picasa.php';

	class home{
        function display()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
                'cookie' => true,
            ));

            $user = $facebook->getUser();
            if ($user)
            {
                //Check User session is set
                // if not than fetch user info and check user exsist. then save user info into session
                //otherwise insert new record and set session
                if(!isset($_SESSION['user_name']))
                {

                    $userinfo = $facebook->api('/me?access_token=' .$facebook->getAccessToken());
                    $db = new Database();
                    $db->connect();

                    // check user exisit if yes then set use_name & picasa_token into session

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
                        $_SESSION['user_name']=$userinfo['username'];
                        $_SESSION['user_id']=$id;

                    }else{
                        $_SESSION['user_name']=$checkUser[0]['username'];
                        $_SESSION['sessionToken']=$checkUser[0]['picasa_token'];
                        $_SESSION['user_id']=$checkUser[0]['id'];

                    }

                }

                $params = array( 'next' => next_logout);
                 $logoutUrl = $facebook->getLogoutUrl($params);
            }
            else
            {
                $params = array(
                    'scope' => 'user_photos',
                );
                $loginUrl = $facebook->getLoginUrl($params);
            }
            include 'Templete/index.php';
        }

        function downloadalbum()
        {
            error_reporting(E_ERROR);
            $albumId=$_REQUEST['selected_checkbox'];
            $move=$_REQUEST['move'];

            if($move==1)
            {
	            $client = picasa::getAuthSubHttpClient();
            }


            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
                'cookie' => true,
            ));

            $userId=$_SESSION['user_id'];
            $basePath="Dump/";

            //if dump folder for particuler user is available then delete old one and make new directory
            if(is_dir($basePath.$userId))
            {
                home::recursive_remove_directory($basePath.$userId,$empty=TRUE);
                rmdir($basePath.$userId);

            }
            mkdir($basePath.$userId);

            $albums=explode(',',$albumId);
            for($i=0;$i<count($albums);$i++)
            {
                $aid=trim($albums[$i]);
                $photos = $facebook->api("/$aid/photos?access_token=".$facebook->getAccessToken());
                $AlbumName=$facebook->api("/$aid?access_token=".$facebook->getAccessToken());
                $temp = str_replace('.', '-', $AlbumName['name']);
                $aname=preg_replace('/[^A-Za-z0-9\-]/', '', $temp);
                if (!is_dir($basePath.$userId.'/'.$aname))
                {
                       mkdir($basePath.$userId.'/'.$aname);
                }
                //Create Album if Move
                if($move==1)
                {
                    $AlubumId=picasa::addAlbum($client,$aname);

                }
                for($j=0;$j<count($photos['data']);$j++)
                {
                    set_time_limit(0);
                    $ext=pathinfo($photos['data'][$j]['source']);
                    copy($photos['data'][$j]['source'],$basePath.$userId.'/'.$aname.'/'.$ext['filename'].'.'.$ext['extension']);


                    if($move==1)
                    {
                        picasa::addPhoto($client,$AlubumId,$basePath.$userId.'/'.$aname.'/'.$ext['filename'].'.'.$ext['extension'],$ext['extension'],$ext['filename']);

                    }
                }

            }
            //______________________________ZIP File_________________________

            if($move==0)
            {
                ini_set("max_execution_time", 300);
                // create object
                $zip = new ZipArchive();
                // open archive
                if ($zip->open($basePath.$userId.'.zip', ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die("Could not open archive");
                }
                // initialize an iterator
                // pass it the directory to be processed
                //echo $basePath.$userId.'/Main/';
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath.$userId.'/'));
                // iterate over the directory
                // add each file found to the archive
                foreach ($iterator as $key=>$value) {

               
                    $key = str_replace("\\", "/", $key);
                    $key = str_replace("\\", "/", $key);
                    $key = str_replace("\\", "/", $key);
                    $key = str_replace("\\", "/", $key);
                    $key = str_replace("\\", "/", $key);

                    $value = str_replace("\\", "/", $value);
                    $value = str_replace("\\", "/", $value);
                    $value = str_replace("\\", "/", $value);
                    $value = str_replace("\\", "/", $value);
                    $value = str_replace("\\", "/", $value);

                    $newKey = str_replace($basePath.$userId."/", "", $key);
                    $temp=explode('/',$newKey);
                    
                    if($newKey!="" and $newKey!="." and $newKey!=".." )
                    {
                       if(isset($temp[1]))
			  {
                          if($temp[1]!=".." and $temp[1]!=".")
			     {    
                       
				//echo $newKey." - ".$key."</br>" ;
			      $zip->addFile(realpath($key), $newKey) or die ("ERROR: Could not add file: $key - $newKey");
				}
			   }
                    }

                }


                $zip->close();
	    }
            
            //delete folder
            if(is_dir($basePath.$userId))
            {
                home::recursive_remove_directory($basePath.$userId,$empty=TRUE);
                rmdir($basePath.$userId);

            }
        }
        function getzip()
        {
            if(isset($_SESSION['user_id']))
            {
                        header("Content-Description: File Transfer");
                        header("Content-Disposition:attachment;filename=".$_SESSION['user_id'].'.zip');
                        header('Content-Type: application/zip');
                        header("Content-Transfer-Encoding: binary");
                        readfile('Dump/'.$_SESSION['user_id'].'.zip' );
            }else{
                echo "error";
            }
        }

        function recursive_remove_directory($directory, $empty=FALSE)
        {
            // if the path has a slash at the end we remove it here
            if(substr($directory,-1) == '/')
            {
                $directory = substr($directory,0,-1);
            }

            // if the path is not valid or is not a directory ...
            if(!file_exists($directory) || !is_dir($directory))
            {
                // ... we return false and exit the function
                return FALSE;

                // ... if the path is not readable
            }elseif(!is_readable($directory))
            {
                // ... we return false and exit the function
                return FALSE;

                // ... else if the path is readable
            }else{

                // we open the directory
                $handle = opendir($directory);

                // and scan through the items inside
                while (FALSE !== ($item = readdir($handle)))
                {
                    // if the filepointer is not the current directory
                    // or the parent directory
                    if($item != '.' && $item != '..')
                    {
                        // we build the new path to delete
                        $path = $directory.'/'.$item;

                        // if the new path is a directory
                        if(is_dir($path))
                        {
                            // we call this function with the new path
                            home::recursive_remove_directory($path);

                            // if the new path is a file
                        }else{
                            // we remove the file
                            unlink($path);
                        }
                    }
                }
                // close the directory
                closedir($handle);

                // if the option to empty is not set to true
                if($empty == FALSE)
                {
                    // try to delete the now empty directory
                    if(!rmdir($directory))
                    {
                        // return false if not possible
                        return FALSE;
                    }
                }
                // return success
                return TRUE;
            }
        }
        function logout()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
                'cookie' => true,
            ));
            $facebook->destroySession();
            session_destroy();
            header("Location:/home");

        }

	function viewalbum()
        {
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));
            $photos = $facebook->api('/'.$_REQUEST['id'].'?fields=photos.fields(source)&access_token='.$facebook->getAccessToken());
            for($i=0;$i<count($photos['photos']['data']);$i++)
            {
                echo "<a href='".$photos['photos']['data'][$i]['source']."'class='fancybox' data-fancybox-group='gallery'></a>";
            }
        }
}
