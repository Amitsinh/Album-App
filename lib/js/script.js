// JavaScript Document
// contains functions for downloading albums and showing image slideshow

/**
 * Gets the images of the album and plays the fancybox slideshow
 *@param Id of the album whose images are to be viewed
 */

function viewalbum(albumId) {


    $('#gallery').load("home/viewalbum", {
            id: albumId,
            type: 'slideshow'
        }, // Load the div#photo with the anchor tags having link to the album images

        function (data) {
            $('a.fancybox').fancybox({ // open fancybox
                cyclic: true,
//                autoPlay: true, //  slideshow will start after opening the first gallery item
                playSpeed: 4000, // 4sec pause between changing next item

                onUpdate: function () {

                    //functions for enabling swipe in touch devices //touchSwype
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

                    /**
                     * Catch each phase of the swipe.
                     * move : we drag the div.
                     * cancel : we animate back to where we were
                     * end : we animate to the next image
                     */

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

                    /**
                     * Manuallt update the position of the imgs on drag
                     */

                    function scrollImages(distance, duration) {
                        imgs.css("-webkit-transition-duration", (duration / 1000).toFixed(1) + "s");

                        //inverse the number we set in the css
                        var value = (distance < 0 ? "" : "-") + Math.abs(distance).toString();

                        imgs.css("-webkit-transform", "translate3d(" + value + "px,0px,0px)");
                    }

                } // touchSwpe

            });
            $('a.fancybox:first').trigger('click'); // Acts as Click event for First Album photo to start the slideshow
        }
    );
};


