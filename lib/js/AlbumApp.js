//AlbumApp Script


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
                if(move==0)
                {
                    $('#dumpLink').show();
                    $("html, body").animate({ scrollTop: 0 }, "slow");

                }
            }
        });
    }
}
//Single Download or Share

function single(val)
{
    showAjaxLoader();
    var id=val.split("_");
    var move=0; //move=1 then share otherwise Download
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
                $("html, body").animate({ scrollTop: 0 }, "slow");

            }

        }
    });

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
    window.location = 'home/getzip';
    $('#dumpLink').hide();

}


