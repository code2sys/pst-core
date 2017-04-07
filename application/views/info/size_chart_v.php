<div class="clear"></div>
<script>
    $(document).on('click', '.fl-chrt', function() {
        var cls = $(this).data('id');
        $('.'+cls).slideToggle( "slow" );
        $(document).prop('title', $(this).data('title'));
    });
    
    $(window).load(function(){
        var url = window.location.href;
        url = url.split('#');
        url = url[1].replace(' ', '-');
        $('.'+url).show();
        $(document).prop('title', $('.'+url).data('title'));
    });
</script>
<div class="sw clear szchart">
    <div class="cntnr">
        <div class="fw szchrt">
            <p class="hdn"><?php echo $brand['name']; ?> Sizing Charts <a href="<?php echo base_url($brand['slug']); ?>">Shop <?php echo $brand['name']; ?></a></p>
            <ul>
                <?php foreach ($sizeChart as $key => $val) { ?>
                    <li class="fl-chrt" data-title="<?php echo $val['title']; ?>" data-id="<?php echo str_replace(' ', '-', $val['url']); ?>">
                        <a class="szimg" href="#<?php echo $val['url']; ?>"><img src="<?php echo base_url($media); ?>/<?php echo $val['image']; ?>"></a>
                        <p><a href="#<?php echo $val['url']; ?>"><?php echo $val['title']; ?></a></p>
                    </li>
                <?php } ?>
            </ul>

            <?php foreach ($sizeChart as $key => $val) { ?>
                <div class="acrdn" id="<?php echo $val['url']; ?>">
                    <div class="acrdn-hdng fl-chrt" data-id="<?php echo $val['url']; ?>" data-title="<?php echo $val['title']; ?>">
                        <div class="h1-sz-ttl"><h1> <span> <i class="optn fa fa-angle-double-down" aria-hidden="true"></i> <?php echo $val['title']; ?> </span></h1>  <a href="<?php echo base_url('shopping/productlist'.$val['catUrl']); ?>">Shop <?php echo $brand['name'].' '.$val['catName']; ?></a></div>
                    </div>
                    <div class="acrdn-cntnt <?php echo str_replace(' ', '-', $val['url']); ?>" style="display:none;" data-title="<?php echo $val['title']; ?>">
                        <table>
                            <?php
                            $size_table = json_decode($val['size_chart']);
                            $cnt = 1;
                            foreach ($size_table as $k => $v) {
                                if ($cnt == 1) {
                                    ?>
                                    <tr>
                                        <?php foreach ($v as $k1 => $v1) { ?>
                                            <th><b><?php echo $v1; ?></b></th>
                                        <?php } ?>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <?php foreach ($v as $k1 => $v1) { ?>
                                            <td><?php echo $v1; ?></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                <?php
                                $cnt++;
                            }
                            ?>
                        </table>
                        <p style="margin-top:10px;"><?php echo $val['content'];?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="clear"></div>
