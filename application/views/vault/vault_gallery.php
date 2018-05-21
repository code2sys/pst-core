<?php

$fancybox_group = isset($fancybox_group) ? $fancybox_group : "gallery";
$fancybox_class = isset($fancybox_class) ? $fancybox_class : "fancybox";

?>
<div class="sw gallery <?php echo $fancybox_group; ?>">
    <div class="container cont-content">
        <div class="row">
            <!--<div class="side-left-img">
                <div class="bg-left">
                </div>
            </div>-->
            <div class="thum">
                <ul>
                    <?php foreach($image as $k=>$v){ ?>
                        <li data-thumb="<?php echo base_url($media); ?>/<?php echo $v['image_name']; ?>">
                            <a class="<?php echo $fancybox_class; ?> pop-img" href="<?php echo base_url($media); ?>/<?php echo $v['image_name']; ?>" data-fancybox-group="<?php echo $fancybox_group; ?>">
                                <div class="overlay-img">
                                    <img src="<?php echo base_url($media); ?>/<?php echo $v['image_name']; ?>">
                                    <span class="fa fa-search-plus"></span>
                                </div>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <!--<div class="side-right-img">
                <div class="bg-right"></div>
            </div>-->
        </div>
    </div>
</div>

<div style="clear: both"></div>

<script>
    //$(document).ready(function(){
    // $('#lightgallery').lightGallery();
    //});
    $(document).ready(function() {
        $(".<?php echo $fancybox_class; ?>").fancybox({
            prevEffect	: 'none',
            nextEffect	: 'none',
            helpers	: {
                title	: {
                    type: 'outside'
                },
                thumbs	: {
                    width	: 50,
                    height	: 50
                }
            }
        });
    });
</script>