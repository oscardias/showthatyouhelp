            <div class="social_media_item">
                <!-- Twitter share -->
                <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical"><?php elang('twitter'); ?></a>
                <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            </div>

            <div class="social_media_item">
                <!-- Facebook share -->
                <a name="fb_share" type="box_count" href="http://www.facebook.com/sharer.php"><?php elang('facebook'); ?></a>
                <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
            </div>

            <div class="social_media_item">
                <!-- Google Buzz share -->
                <a href="http://www.google.com/buzz/post" class="google-buzz-button" title="Google Buzz"
                   data-url="<?php echo urlencode($post_url); ?>" data-locale="en" data-button-style="normal-count"></a>
                <script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>
            </div>
            <div class="social_media_item">
                <!-- Digg share -->
                <script type="text/javascript">
                (function() {
                var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'http://widgets.digg.com/buttons.js';
                s1.parentNode.insertBefore(s, s1);
                })();
                </script>
                <a class="DiggThisButton DiggMedium"></a>
            </div>
            <div class="social_media_item">
                <!-- Stumbleupon share -->
                <script type="text/javascript" src="http://www.stumbleupon.com/hostedbadge.php?s=5"></script>
            </div>
            <div class="social_media_item">
                <!-- Reddit share -->
                <script type="text/javascript" src="http://reddit.com/static/button/button2.js"></script>
            </div>
            <div class="clear"></div>
