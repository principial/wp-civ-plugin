// IIFE - Immediately Invoked Function Expression
(function(yourcode) {

    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function($, window, document) {

    // The $ is now locally scoped

    // Listen for the jQuery ready event on the document
    $(function() {

        // The DOM is ready!
        //Setup variables
        var imageWrapper = $('#category-image-wrapper'),
            videoWrapper = $('#category-video-wrapper'),
            videoThumb = $('#category-video-thumb'),
            videoInput = $('.term-video-input');

        //Activate media upload functionality
        ct_media_upload('.ct_tax_media_button.button');

        //Get video link and separate depends on service
        $('#ct_tax_video_button').on('click', function(e){
            var input = $(this).closest('.form-field').find('#category-video-link'),
                link = input.val();
            videoInput.removeClass('form-invalid');
            if (link.indexOf("youtu.be") >= 0) {
                get_thumb_youtube(link);
            } else if (link.indexOf("vimeo.com") >= 0) {
                get_thumb_vimeo(link);
            } else {
                videoInput.addClass('form-invalid');
            }
        });

        //Remove image and clear input field
        $('.ct_tax_media_remove').on('click', function(){
            $('#category-image-id').val('');
            imageWrapper.html('');
        });

        //Remove video, thumbnail and clear input fields
        $('.ct_tax_video_remove').on('click', function(){
            videoThumb.val('');
            $('#category-video-link').val('');
            videoWrapper.html('');
            videoInput.removeClass('form-invalid');
        });

        // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (typeof settings.data !== 'undefined' && settings.data.length > 0) {
                var queryStringArr = settings.data.split('&');
                if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                    var xml = xhr.responseXML;
                    $response = $(xml).find('term_id').text();
                    if ($response != "") {
                        // Clear the thumb image
                        imageWrapper.html('');
                        videoWrapper.html('');
                        videoInput.removeClass('form-invalid');
                    }
                }
            }
        });

        // Add image media upload activate
        function ct_media_upload(button_class) {
            var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;
            $(button_class).on('click', function(e) {
                var button_id = '#'+$(this).attr('id');
                var send_attachment_bkp = wp.media.editor.send.attachment;
                var button = $(button_id);
                _custom_media = true;
                wp.media.editor.send.attachment = function(props, attachment){
                    if ( _custom_media ) {
                        $('#category-image-id').val(attachment.id);
                        imageWrapper.html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                        $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                    } else {
                        return _orig_send_attachment.apply( button_id, [props, attachment] );
                    }
                }
                wp.media.editor.open(button);
                return false;
            });
        }

        //Get thumbnail from youtube embed link
        function get_thumb_youtube(link) {
            var id = /[^/]*$/.exec(link)[0],
                imgLink = 'https://img.youtube.com/vi/'+id+'/0.jpg';
            videoWrapper.html('<img class="custom_media_image" src="'+imgLink+'" style="margin:0;padding:0;max-height:100px;float:none;" />');
            videoThumb.val(imgLink);
        }

        //Get thumbnail from vimeo link
        function get_thumb_vimeo(link) {
            var id = /[^/]*$/.exec(link)[0],
                imgLink = '';
            // Endpoint: https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/
            $.getJSON('https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/' + id, {
                    format: "json",
                    width: "640"
                },
                function(data) {
                    imgLink = data.thumbnail_url;
                    videoWrapper.html('<img class="custom_media_image" src="'+imgLink+'" style="margin:0;padding:0;max-height:100px;float:none;" />');
                    videoThumb.val(imgLink);
                });
            return false;
        }

    });

    // The rest of the code goes here!

}));
