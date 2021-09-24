<?php $facebookId = \Config::get('facebook_tracking.id'); ?>

<?php if (null != $facebookId && count($events)): ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','//connect.facebook.net/en_US/fbevents.js');

        fbq('init', <?php echo $facebookId; ?>);
        <?php foreach ($events as $event): ?>
            <?php echo $event->outputJavaScriptLine(); ?>
        <?php endforeach; ?>
    </script>
    <noscript>
        <?php foreach ($events as $event): ?>
            <?php echo $event->outputImageTag($facebookId); ?>
        <?php endforeach; ?>
    </noscript>
    <!-- End Facebook Pixel Code -->
<?php endif; ?>