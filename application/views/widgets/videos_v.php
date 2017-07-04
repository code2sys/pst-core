<?php if ($mainVideo != '') { ?>
    <div class="content_section rmv">
        <?php if ($mainVideo != '') { ?>
            <div class="main-vdo">
                <iframe src="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-id="<?php echo $mainVideo; ?>" id="mainVideo" frameborder="0" allowfullscreen></iframe>
                <ul>
                    <li><strong>Share :</strong>
                        <div class="ggl" style="min-width:100px;">
                            <div class="fb-share-button" data-href="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-layout="button_count"></div>
                        </div>
                        <div class="ggl">
                            <div class="g-plus fixwdth" data-action="share" data-href="https://www.youtube.com/embed/<?php echo $mainVideo; ?>" data-width="250"></div>
                        </div>
                    </li>
                    <li class="subs"><strong>Subscribe to us :</strong>
                        <?php
                        $link_array = explode('/', $SMSettings['sm_ytlink']);
                        ?>
                        <div class="g-ytsubscribe" data-channelid="<?php echo end($link_array); ?>" data-layout="default" data-count="default"></div>
                    </li>
                </ul>
            </div>
            <script type="text/javascript">gapi.plus.go();</script>
        <?php } ?>
        <div class="rltv-vdo">
            <ul>
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
<?php
}
?>