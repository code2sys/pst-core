<?php
/*
 * In application/views/benz_views, differences
 *
 *                                 <iframe width="560" height="400" src="https://www.youtube.com/embed/<?php echo $mainVideo['video_url']; ?>" data-id="<?php echo $mainVideo['video_url']; ?>" id="mainVideo" frameborder="0" allowfullscreen=""></iframe> -- notice how it's takign apart that main Video URL...
 *
 * They defined $link_array = explode('/', $SMSettings['sm_ytlink']);
 * and then stuck channel ID as
 * echo end($link_array);
 */

if (!defined("YOUTUBE_CHANNEL")) {
    $CI =& get_instance();
    $CI->load->model("admin_m");
    $smsettings = $CI->admin_m->getSMSettings();
    if (array_key_exists("sm_ytlink", $smsettings) && $smsettings["sm_ytlink"] != "") {
        define('YOUTUBE_CHANNEL', basename($smsettings["sm_ytlink"]));
    } else {
        define('YOUTUBE_CHANNEL', '');
    }
}

?>
<div class="<?php echo $class_name; ?>">
    <div class="lft">
        <iframe src="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-id="<?php echo $mainVideo; ?>" id="mainVideo" frameborder="0" allowfullscreen=""></iframe>
        <ul>
            <li><strong>Share :</strong>
                <div class="fb-share-button" data-href="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-layout="button_count"></div>
                <div class="ggl">
                    <div class="g-plus fixwdth" data-action="share" data-href="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-width="250"></div>
                </div>
            </li>
            <li class="subs"><strong>Subscribe to us :</strong>
                <div class="g-ytsubscribe" data-channel="<?php echo YOUTUBE_CHANNEL; ?>" data-layout="default" data-count="default"></div>
            </li>
        </ul>
    </div>
    <div class="ryt">
        <ul <?php if (isset($rltdvdo_class)): ?>class="<?php echo $rltdvdo_class; ?>"<?php endif; ?>>
            <li onClick="showVideo('<?php echo $mainVideo; ?>', '<?php echo $mainTitle; ?>');" id="<?php echo $mainVideo; ?>" style="display:none;">
                <img class="ply" src="/qatesting/newassets/images/play.png">
                <img src="http://img.youtube.com/vi/<?php echo $mainVideo; ?>/default.jpg" class="active">
                <p><?php echo $mainTitle; ?></p>
            </li>
            <?php foreach ($video as $k => $v) { ?>
                <li onClick="showVideo('<?php echo $v['video_url']; ?>', '<?php echo $v['title']; ?>');" id="<?php echo $v['video_url']; ?>">
                    <img class="ply" src="/qatesting/newassets/images/play.png">
                    <img src="http://img.youtube.com/vi/<?php echo $v['video_url']; ?>/default.jpg" class="active">
                    <p><?php echo $v['title']; ?></p>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php if ($autoplay): ?>
<script type="application/javascript">
    $(document).ready(function() {
        try {
            if(gapi) {
                gapi.plus.go();
            } else {
                console.log("gapi not found");
            }
        } catch(err) {
            console.log("Error with GAPI: " + err);
        }
    });
</script>
<?php endif; ?>