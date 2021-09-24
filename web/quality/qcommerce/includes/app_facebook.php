<?php
$redeSocial = RedeQuery::create()->filterByLink('%facebook%')->findOne();
$url = "//www.facebook.com/plugins/likebox.php?href=" . (!is_null($redeSocial) ? urlencode($redeSocial->getLink()) : '') . "&amp;width=480&amp;height=170&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false";
?>


<div class="like-box-facebook pull-right">
    <iframe src="<?php echo $url; ?>" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
</div>
