<?php if ($mainVideo != '') { ?>
    <div class="content_section rmv">
        <?php if ($mainVideo != '') { ?>
            <?php
            $CI =& get_instance();
            echo $CI->load->view("master/embedded_videos", array(
                "class_name" => "main-vdo",
                "mainVideo" => $mainVideo,
                "mainTitle" => $mainVideo['title'],
                "video" => $video,
                "rltdvdo_class" => "rltv-vdo",
                "autoplay" => true,
                "mainVideo_word" => $mainVideo_word,
                "id_extra" => $id_extra
            ), true);
            ?>
            <?php } ?>
    </div>
<?php
}
?>