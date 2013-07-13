<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<!--[if IE 8]><html class="no-js lt-ie9" lang="en" ><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en" xmlns="http://www.w3.org/1999/html"><!--<![endif]-->

<head>
    <title>Album App</title>
<!--    Foundation-->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Album App</title>
    <link rel="stylesheet" href="lib/css/foundation.css" />
    <script src="lib/js/vendor/custom.modernizr.js"></script>

<!--    Glisse-->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <link rel="stylesheet" href="lib/css/glisse.css" />
    <link rel="stylesheet" href="lib/css/app.css" />
<style>
    .ajax_loader {
        background: url("lib/images/spinner_squares_circle.gif") no-repeat center center transparent;
        width:100%;
        height:100%;
    }
</style>

</head>

<!--Script-->
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="lib/js/ajaxloader.min.js"></script>
<script src="lib/js/glisse.js"></script>
<script src="lib/js/foundation.min.js"></script>
<script src="lib/js/foundation/foundation.interchange.js"></script>
<script src="lib/js/ajaxloader.js"></script>

<script>

    $(document).ready(function() {
        $('#dumpLink').hide();

    });

    $(function () {
        $('.tl').glisse({speed: 200, changeSpeed: 250, effect:'bounce', fullscreen: true});
        $('#changefx').change(function() {
            var val = $(this).val();
            $('.tl').each(function(){
                $(this).data('glisse').changeEffect(val);
            });
        });
    });
    document.write('<script src=' +
            ('__proto__' in {} ? 'lib/js/vendor/zepto' : 'lib/js/vendor/jquery') +
            '.js><\/script>')
    $(document).foundation();

    function downloadalbum(id)
    {
        var Checkbox="";
        var move=0;


        if(id=='da' || id=='ma')
        {
            $('input:checkbox').attr('checked','checked');
            $('input[type=checkbox]').each(function () {
                if(Checkbox=="")
                {
                    Checkbox=$(this).val();

                }else
                {
                    Checkbox=Checkbox+","+$(this).val();
                }
            });
        }else if(id=='ds' || id=='ms')
        {
            $('input[type=checkbox]').each(function () {
                if(this.checked)
                {
                    if(Checkbox=="")
                    {
                        Checkbox=$(this).val();

                    }else
                    {
                        Checkbox=Checkbox+","+$(this).val();
                    }
                }
            });
        }
        if(id=='ma' || id=='ms')
        {
            move=1;
        }
        if((id=="ds" || id=='ms') && Checkbox=="")
        {
            alert('Please Select atleast one Album.');
        }else
        {
            showAjaxLoader();
            $.ajax({
            type:"POST",
            url: "home/downloadalbum",
            data:"selected_checkbox="+Checkbox+"+&move="+move,
            success:function(Result)
            {
                hideAjaxLoader();
                $('input:checkbox').removeAttr('checked');
                if(Result==1)
                {
                    $('#dumpLink').show();
                }
            }
            });
        }
    }
    function zip()
    {
        window.location = 'home/getzip';
        
        $('#dumpLink').hide();

    }
    function showAjaxLoader(){
            ajaxProgress = new ajaxLoader($('body'),{height:$(document).height(),width:$(document).width()});
    }

    function hideAjaxLoader(){
        if (ajaxProgress)
            ajaxProgress.remove();
    }
    function single(val)
    {
        showAjaxLoader();
        var id=val.split("_");
        var move=0;
        if(id[1]=='p')
        {
            move=1;
        }
        $.ajax({
            type:"POST",
            url: "home/downloadalbum",
            data:"selected_checkbox="+id[0]+"+&move="+move,
            success:function(Result)
            {
                hideAjaxLoader();
                if(move==0)
                {
                    $('#dumpLink').show();
                }

            }
        });

    }
</script>

<body>

<div class="row" style="max-width: 100%;background-color: #2BA6CB">
    <div class="large-4 columns" style="margin-left: 50px;"><h2 style="color:#f5f5f5">Album App</h2>        </div>

    <div class="button-bar large-7 columns" style="margin-top: 15px">
        <ul class="button-group" style="float: right">

        <?php
            if(isset($_SESSION['sessionToken']))
            {
                if($_SESSION['sessionToken']=="")
                {
        ?>
                    <li><a href='<?php echo picasa_login ?>'  class="button secondary">Connect to Picasa</a></li>
           <?php }

             }else if(isset($_SESSION['user_name'])){?>

                <li><a href='<?php echo picasa_login ?>'  class="button secondary">Connect to Picasa</a></li>
       <?php }

        if(isset($_SESSION['user_name'])){?>
            <li><a  href='<?php echo $logoutUrl ?>'  class="button secondary">Logout</a></li>
            <?php }
        ?>

        </ul>
   </div>
</div>

    <?php if(!$user){ ?>

            <div class="row" style="border-style: solid;border-width: 1px;border-color: #d9d9d9;margin-bottom: 1.25em;padding: 1.25em;">

                <div class="large columns">

                    <!-- Grid Example -->
                    <div class="row">
                        <div class="large-8 columns">
                            <div style="margin-top: 130px">
                                <p>Album App is a free service that allows you to move your facebook profile's Album to Picasa.It also allows you to  download all your photos into a single Zip archive!
                                <br><br>Try it! It's free.</p>
                                    <a href="<?php echo $loginUrl; ?>"><img src="lib/images/login2.png"></a>
                            </div>
                        </div>
                        <div class="large-4 columns">
                            <p><img style="width: 100%;" src="lib/images/box.jpg" data-interchange="[lib/images/box.jpg, (default)], [lib/images/box.jpg, (screen and (max-width: 568px))], [lib/images/box.jpg, (small)], [lib/images/box.jpg, (medium)], [lib/images/box.jpg, (large)]" data-uuid="ecb8eefd-9a44-a755-6fb1-b71f655a2c7a"></p>
                        </div>
                    </div>
                    <br><br>
                </div>


    <?php } else {?>
    <div class="row" style="max-width: 100%;background-color: #ffffff;margin-top: 5px">

            <div id="dumpLink" class="panel large-5 columns" style="display:none;padding: 0px">
                <p>File Link &nbsp;&nbsp; <a id="zip_link" name="zip_link" onclick="zip();">http://<?php echo $_SERVER['HTTP_HOST'] ?>/AlbumApp/Dump/<?php echo $_SESSION['user_id'] ?>.zip</a></p>
            </div>
            <ul class="button-group" style="float: right">
                <li><input id='da' type="button" class="small button" value="Download All" onclick="downloadalbum(this.id);"></li>
                <li><input id="ds" type="button" class="small button" value="Download Selected" onclick="downloadalbum(this.id);"></li>
                <?php
                if(isset($_SESSION['sessionToken']))
                {
                    if($_SESSION['sessionToken']!="")
                    {
                ?>
                <li><input id="ma" type="button" class="small button" value="Move All" onclick="downloadalbum(this.id);"></li>
                <li><input id="ms" type="button" class="small button" value="Move Selected" onclick="downloadalbum(this.id);"></li>
                        <?php
                    }
                }
                ?>
            </ul>
                </div>

    <div id="content">
            <?php
//
            if ($user) {?>
                <script>showAjaxLoader();</script>

               <?php
//                     Proceed knowing you have a logged in user who's authenticated.
                    $albums = $facebook->api('/me?fields=albums.fields(cover_photo,name)&access_token='.$facebook->getAccessToken());

                    for($i=0;$i<count($albums['albums']['data']);$i++)
                    {
                        $photos = $facebook->api("/{$albums['albums']['data'][$i]['id']}/photos");
                        if($photos['data'])
                        {
                        ?>
                            <ul class="stack">
                                <input type="checkbox" class="album_check" name="album_checkbox[]" value='<?php echo $albums['albums']['data'][$i]['id'];?>' style="z-index:2;position: absolute;left:15px;top:14px">
                               
                                <?php
                                    if(isset($_SESSION['sessionToken']))
                                    {
                                        if($_SESSION['sessionToken']!="")
                                    {   ?>
                                <input type="button" style="background-image:url(lib/images/icon2.png);height:18px;width:20px;z-index:3;position: absolute;top:14px;right:63px;cursor: pointer" id="<?php echo $albums['albums']['data'][$i]['id'];?>_p" onclick="single(this.id)">
                            <?php
                                }
                            } ?>
				 <input type="button"  style="background-image:url(lib/images/icon1.png);height:18px;width:20px;z-index:4;position: absolute;top:14px;right:40px;cursor: pointer" id="<?php echo $albums['albums']['data'][$i]['id'];?>_d" onclick="single(this.id)">
                            <?php 
				 for($j=0;$j<count($photos['data']);$j++)
                            {
                                set_time_limit(0);
                                if($j==0 or $j==(count($photos['data'])-1))
                                {   ?>
                                    <li><img src="<?php  echo $photos['data'][$j]['source']?>" rel="<?php echo $i ?>" style="height: 200px;width: 200px;z-index:1" data-glisse-big="<?php  echo $photos['data'][$j]['source']?>" class="tl" title="<?php echo $albums['albums']['data'][$i]['name']?> " /></li>
                                <?php
                                }else{  ?>
                                    <li><img rel="<?php echo $i ?>" style="height: 200px;width: 200px;z-index:1" data-glisse-big="<?php  echo $photos['data'][$j]['source']?>" class="tl"/></li>
                                <?php
                                }
                            }?>
                        </ul>
                <?php }
                }   ?>
                <script>hideAjaxLoader();</script>

          <?php } ?>
    </div><br>

    <?php }   ?>

  </body>

</html>