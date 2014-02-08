// Vimeo Feed

var apiEndpoint = 'http://www.vimeo.com/api/v2/';
        var oEmbedEndpoint = 'http://www.vimeo.com/api/oembed.json'
        var videosCallback = 'setupGallery';
        var vimeoUsername = 'user3289507';
        
        // Get the user's videos
        $(document).ready(function() {
            $.getScript(apiEndpoint + vimeoUsername + '/videos.json?callback=' + videosCallback);
        });
        
        function getVideo(url) {
            $.getScript(oEmbedEndpoint + '?url=' );
        };
        
        function setupGallery(videos) {
            
            // Load the first video
            getVideo(videos[0].url);
            
            // Add the videos to the gallery
            for (var i = 0; i < videos.length; i++) {
                var html = '<li><a href="' + videos[i].url + '" target="_new"><img src="' + videos[i].thumbnail_medium + '" class="thumb" /></a></li>';
                $('#feed_vimeo ul').append(html);
            }
            
        };