//AlbumApp Script

//Download All / Move All /Download Selected/Move Selected

function downloadalbum(id)
{
    var Checkbox="";
    var move=0; //move=1 then share otherwise Download

    //Download All / Move All /Download Selected/Move Selected
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
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#msg').html('Please Select at least one Album.');
        $('#alertbox').removeClass();
        $('#alertbox').addClass('alert-box');
        $('#alertbox').addClass('alert');
        $('#alertbox').show();
    }else
    {
        proDownload(Checkbox,move);

    }
}

// Download With progressbar
function proDownload(Checkbox,move)
{
    showAjaxLoader();
    $('.ajax_loader').hide();
    $('.progress').show();
    $("html, body").animate({ scrollTop: 0 }, "slow");

    $.getJSON('album/albumdetail?selected_checkbox='+Checkbox+'&move='+move, function(data) {
        var totalPhotos=data[0]['all']
        var pro=100/totalPhotos;
        cur=0;

        var totalAlbum=data.length;
        $.each(data, function(key, val) {
            var albumName=val.name;
            var total=val.total;
            var left=total;
            $.getJSON('album/createAlbum?name='+val.name+'&move='+move+'&id='+val.id, function(data) {

                $.each(data, function(i, photo) {
                    if(move==1)
                    {
                        var url="name="+albumName+"&move="+move+"&source="+photo.source+'&picasaId='+photo.picasaId;
                    }else{
                        var url="name="+albumName+"&move="+move+"&source="+photo.source;
                    }
                    $.ajax({
                        type:"POST",
                        url: "album/saveAlbum",
                        data:url,
                        success:function(Result){
                            cur=cur+1;
                            left=left-1;
                            $('.meter').width((pro * (cur))+'%');
                            $('.pr_photos').html(left+'/'+total +' Left');
                            $('.pr_album').html(albumName);
                            if(key==totalAlbum-1 && left==0)
                            {
                                $('.meter').width('100%');
                                $('.pr_photos').html('Done');
                                var t=setTimeout(function(){
                                    //Create Zip
                                    $.ajax({
                                        type:"POST",
                                        url: "album/CreateZip",
                                        data:"move="+move,
                                        success:function(Result){
                                            hideAjaxLoader();
                                            $('.progress').hide();
                                            $('.meter').width('0%');
                                            $('.pr_photos').html('');
                                            $('.pr_album').html('Preparing');
                                            $('input:checkbox').removeAttr('checked');
                                            if(move==0)
                                            {
                                                $.fancybox.open({
                                                    href     : "#dumpLink",
                                                    autoSize : true,
                                                    fitToView: true
                                                });
                                            }else
                                            {
                                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                                $('#msg').html('Album Shared successfully.');
                                                $('#alertbox').removeClass();
                                                $('#alertbox').addClass('alert-box');
                                                $('#alertbox').addClass('success');
                                                $('#alertbox').show();
                                            }
                                        }
                                    });
                                },1000);

                            }
                        }
                    });
                });
            });
        });
    });
}

// Download or Share Single Photo
function SinglePhoto(title)
{
    var move=0;
    if(title=='Share')
    {
        move=1;
    }
    showAjaxLoader();
    $.ajax({
        type:"POST",
        url: "album/downloadalbum",
        data:"photo_id="+$('#photo_id').val()+"+&move="+move,
        success:function(data)
        {
            hideAjaxLoader();
            if(data!='error')
            {
                if(move==0)
                {
                    $.fancybox.open({
                        href     : "#dumpLink",
                        autoSize : true,
                        fitToView: true
                    });
                }else
                {
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#msg').html('Photo Shared successfully.');
                    $('#alertbox').removeClass();
                    $('#alertbox').addClass('alert-box');
                    $('#alertbox').addClass('success');
                    $('#alertbox').show();
                }
            }else
            {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#msg').html('Their is some error, Try again.');
                $('#alertbox').removeClass();
                $('#alertbox').addClass('alert-box');
                $('#alertbox').addClass('alert');
                $('#alertbox').show();
            }
        }
    });

}

function alertclose()
{
    $('#alertbox').hide('slow');
}


//Single Album  Download or Share

function single(val)
{
    showAjaxLoader();
    var id=val.split("_");
    var move=0; //move=1 then share otherwise Download
    if(id[1]=='p')
    {
        move=1;
    }
    proDownload(id[0],move);
}

//Album Fullscreen View
function viewalbum(albumId) {


    $('#gallery').load("home/viewalbum", {
            id: albumId,
            type: 'slideshow'
        },function (data) {
            $('a.fancybox').fancybox({
                cyclic: true,
                autoPlay: true,
                playSpeed: 4000,
                onUpdate: function () {
                    var IMG_WIDTH = 500,
                        currentImg = 0,
                        speed = 500,
                        imgs,
                        swipeOptions = {
                            triggerOnTouchEnd: true,
                            swipeStatus: swipeStatus,
                            allowPageScroll: "vertical",
                            threshold: 75
                        };

                    $(function () {
                        imgs = $(".fancybox-skin");
                        imgs.swipe(swipeOptions);
                    });

                    function swipeStatus(event, phase, direction, distance) {
                        //If we are moving before swipe, and we are going Lor R in X mode, or U or D in Y mode then drag.
                        if (phase == "move" && (direction == "left" || direction == "right")) {
                            var duration = 0;

                            if (direction == "left") {
                                scrollImages((IMG_WIDTH * currentImg) + distance, duration);
                            } else if (direction == "right") {
                                scrollImages((IMG_WIDTH * currentImg) - distance, duration);
                            }

                        } else if (phase == "cancel") {
                            scrollImages(IMG_WIDTH * currentImg, speed);
                        } else if (phase == "end") {
                            if (direction == "right") {
                                $.fancybox.prev();
                            } else if (direction == "left") {
                                $.fancybox.next();
                            }
                        }
                    }
                    function scrollImages(distance, duration) {
                        imgs.css("-webkit-transition-duration", (duration / 1000).toFixed(1) + "s");

                        //inverse the number we set in the css
                        var value = (distance < 0 ? "" : "-") + Math.abs(distance).toString();

                        imgs.css("-webkit-transform", "translate3d(" + value + "px,0px,0px)");
                    }

                }
            });
            $('a.fancybox:first').trigger('click'); // Acts as Click event for First Album photo to start the slideshow
        }
    );
}

//Ajax loader

function showAjaxLoader(){
    ajaxProgress = new ajaxLoader($('body'),{height:$(document).height(),width:$(document).width()});
}

function hideAjaxLoader(){
    if (ajaxProgress)
        ajaxProgress.remove();
}


//Zip Download

function zip()
{
    window.location = 'album/getzip';
    $.fancybox.close();
    $('#dumpLink').hide();
}

function email()
{
    $.ajax({
        type:"POST",
        url: "home/email",
        success:function(data)
        {
 	     $.fancybox.close();
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $('#msg').html('Email Sent at '+data);
            $('#alertbox').removeClass();
            $('#alertbox').addClass('alert-box');
            $('#alertbox').addClass('success');
            $('#alertbox').show();
        }
    });
}


