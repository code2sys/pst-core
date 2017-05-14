<!-- SIDEBAR -->
<?php if ($custom_sidebar) { ?>
    <div class="sidebar cstm-sdbr">
        <h3 class="head-tx fiter-menu fiter-menu1">FILTER BAR
            <span class="glyphicon glyphicon-filter"></span>
        </h3>

        <div class="fltrbx section-fiter flat prdy-dv prdy-dv1 drop-blok">
            <?php echo @$sidebarpieces; ?>
        </div>
    </div>
<?php } else { ?>
    <div class="sidebar">
        <?php echo @$sidebarpieces; ?>
    </div>
<?php } ?>

<!-- END SIDEBAR -->
