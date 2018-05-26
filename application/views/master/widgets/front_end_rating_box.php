<img class="lazyOwl" alt="Product Image" src="<?php echo site_url('productimages/'.$val['images'][0]['path']);?>" style="display: inline;">
<h2><?php echo $val['label'];?></h2>
<p>
    <?php for( $i=1; $i<=$val['rating']; $i++ ) { ?>
        <i class="fa fa-star"></i>
    <?php } ?>
</p>
<p><?php echo substr($val['review'], 0, 75 ).'...';?></p>
<a href="<?php echo site_url('shopping/item/'.$val['part_id']);?>" class="btn btn-primary btn-secc">Check Details</a>