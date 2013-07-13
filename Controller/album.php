<?php
require 'lib/facebook-php-sdk-master/src/facebook.php';
require 'picasa.php';


    class album{

        //Get Album Detail
        function albumdetail()
        {
            //if dump folder for particuler user is available then delete old one and make new directory
            if(is_dir(basePath.userId))
            {
                album::recursive_remove_directory(basePath.userId,$empty=TRUE);
                rmdir(basePath.userId);
            }
            mkdir(basePath.userId);

            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));

            $albumId=$_REQUEST['selected_checkbox'];
            $albums=explode(',',$albumId);
            $total="";
            $result=array();
            for($i=0;$i<count($albums);$i++)
            {
                set_time_limit(0);
                $aid=trim($albums[$i]);
                $photos = $facebook->api("/$aid/?fields=name,photos.fields(images)&access_token=".$facebook->getAccessToken());
                $temp = str_replace('.', '-', $photos['name']);
                $aname=preg_replace('/[^A-Za-z0-9\-]/', '', $temp);
                $result[$i]['id']=$aid;
                $result[$i]['name']=$aname;
                $result[$i]['total']=count($photos['photos']['data']);
                $total=$total+count($photos['photos']['data']);
            }
            $result[0]['all']=$total;
            echo json_encode($result);
        }

        //Create Album to picasa.
        function createAlbum()
        {
            $name=$_REQUEST['name'];
            $move=$_REQUEST['move'];
            $AlubumId="";
            $result=array();
            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));

            if (!is_dir(basePath.userId.'/'.$name))
            {
                mkdir(basePath.userId.'/'.$name);
            }
            if($move==1)
            {
                $client = picasa::getAuthSubHttpClient();
                $AlubumId=picasa::addAlbum($client,$name);
            }
            $aid=trim($_REQUEST['id']);
            $photos = $facebook->api("/$aid/photos?access_token=".$facebook->getAccessToken());
                for($j=0;$j<count($photos['data']);$j++)
                {
                    set_time_limit(0);
                    $result[$j]['source']=$photos['data'][$j]['images'][0]['source'];
                    if($move==1)
                    {
                        $result[$j]['picasaId']=$AlubumId;
                    }
                }
            echo json_encode($result);
        }

        //Save photos
        function saveAlbum()
        {
            $source=$_REQUEST['source'];
            $move=$_REQUEST['move'];
            $name=$_REQUEST['name'];


            set_time_limit(0);
            $ext=pathinfo($source);
            copy($source,basePath.userId.'/'.$name.'/'.$ext['filename'].'.'.$ext['extension']);

            if($move==1)
            {
                $client = picasa::getAuthSubHttpClient();
                picasa::addPhoto($client,$_REQUEST['picasaId'],basePath.userId.'/'.$name.'/'.$ext['filename'].'.'.$ext['extension'],$ext['extension'],$ext['filename'],'Normal');
            }
        }

        //Create Zip
        function createZip()
        {
            $move=$_REQUEST['move'];
            album::zip($move,'');
        }

        function zip($move,$type)
        {
            if($move==0)
            {
                ini_set("max_execution_time", 300);
                // create object
                $zip = new ZipArchive();
                // open archive
                $_SESSION['fileName']=basePath.userId.time().'.zip';
                if ($zip->open($_SESSION['fileName'], ZIPARCHIVE::OVERWRITE) !== TRUE) {
                    die("Could not open archive");
                }
                // initialize an iterator
                // pass it the directory to be processed
                //echo $basePath.$userId.'/Main/';
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(basePath.userId.'/'));
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

                    $newKey = str_replace(basePath.userId."/", "", $key);
		      if($type=='single')
		      {
	  			if($newKey!="" and $newKey!=".." and $newKey!=".")
                    		{
                        		$zip->addFile(realpath($key), $newKey) or die ("ERROR: Could not add file: $key");
                    		}
		      }else{

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

                }


                $zip->close();
		 echo "1";

            }else{
		echo "0";
	     }
            //delete folder
            if(is_dir(basePath.userId))
            {
                album::recursive_remove_directory(basePath.userId,$empty=TRUE);
                rmdir(basePath.userId);

            }
        }

        //For single Photo
        function downloadalbum()
        {
            error_reporting(E_ERROR);
            $move=$_REQUEST['move'];
            //if dump folder for particuler user is available then delete old one and make new directory

            if(is_dir(basePath.userId))
            {
                album::recursive_remove_directory(basePath.userId,$empty=TRUE);
                rmdir(basePath.userId);

            }
            mkdir(basePath.userId);

            $facebook = new Facebook(array(
                'appId'  => appId,
                'secret' => appSecret,
            ));

            set_time_limit(0);
            $photoId=trim($_REQUEST['photo_id']);
            $SinglePhoto= $facebook->api("/$photoId?access_token=".$facebook->getAccessToken());
            if(isset($SinglePhoto['images']))
            {
                $ext=pathinfo($SinglePhoto['images'][0]['source']);
                copy($SinglePhoto['images'][0]['source'],basePath.userId.'/'.$ext['filename'].'.'.$ext['extension']);
                if($move==1)
                {
                    $client = picasa::getAuthSubHttpClient();
                    picasa::addPhoto($client,'',basePath.userId.'/'.$ext['filename'].'.'.$ext['extension'],$ext['extension'],$ext['filename'],'single');
                }
                echo 'success';
            }else{
                echo 'error';
            }
            album::zip($move,'single');
        }

        //Download Zip file
        function getzip()
        {
            if(isset($_SESSION['user_id']))
            {
                header("Content-Description: File Transfer");
                header("Content-Disposition:attachment;filename=".userId.'.zip');
                header('Content-Type: application/zip');
                header("Content-Transfer-Encoding: binary");
                readfile($_SESSION['fileName']);
            }else{
                echo "error";
            }
        }

        //Recursive Remove directory
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
                            album::recursive_remove_directory($path);

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
    }
