<?php

/*
 * Inputs:
 * $master_class = "fltrbar search-two my-wdt"
 *
 * $subclass = ""
 *
 *
 */

if (!isset($subclass)) {
    $subclass = "";
}

if (!isset($innersubclass)) {
    $innersubclass = "";
}

if (!isset($no_fify)) {
    $no_fify = false;
}

?>
<?php if (isset($master_class) && $master_class != ""): ?>
<div class="<?php echo $master_class; ?>">
    <?php endif; ?>
    <div class="col-md-12 text-center <?php echo $subclass; ?>">
        <h4 style="margin:0 0 20px">RECENTLY VIEWED</h4>
    </div>
    <div class="fltrbx <?php echo $innersubclass; ?>">
        <?php foreach ($recentlyMotorcycle as $recently) {

            $motorcycle_image = $recently['image_name'];
            if ($recently['external'] == 0) {
                $motorcycle_image = base_url().'media/'. $motorcycle_image;
            }
            if ($motorcycle_image == "" || is_null($motorcycle_image) || $motorcycle_image == base_url().'media/') {
                $motorcycle_image = "/assets/image_unavailable.png";
            }

            ?>
            <div class="col-md-12 text-center padg">
                <a class="<?php if (!$no_fify): ?>fify<?php endif; ?>" href="<?php echo base_url(strtolower($recently['type']) . '/' . $recently['url_title'] . '/' . $recently['sku']); ?>">
                    <img class="rvm" src=" <?php echo $motorcycle_image; ?>" />
                </a>
                <a class="<?php if (!$no_fify): ?>fify<?php endif; ?>" href="<?php echo base_url(strtolower($recently['type']) . '/' . $recently['url_title'] . '/' . $recently['sku']); ?>"><h1 class="head-txt"><?php echo $recently['title']; ?></h1></a>
                <!--<p><?php echo $recently['title']; ?></p>-->
                <?php if( $recently['call_on_price'] == '1' ||  ($motorcycle['retail_price'] == 0 && $motorcycle['sale_price'] == 0) ) { ?>
                    <p class="cfp">Call For Price</p>
                    <?php
                } else {
                    if ($recently['sale_price'] > 0 && $recently['sale_price'] !== "0.00" && $recently['sale_price'] != $recently['retail_price']) { ?>
                        <?php if ($motorcycle['retail_price'] > 0): ?>
                        <p>Retail Price: &nbsp; <span
                                class="strikethrough">$<?php echo number_format($recently['retail_price'], 2); ?></span>
                        </p>
                            <?php endif; ?>
                        <p>Sale Price: &nbsp; &nbsp;<span
                                class="redtext">$<?php echo number_format($recently['sale_price'], 2); ?></span></p>
                    <?php } else { ?>
                        <p>Retail Price: &nbsp; $<?php echo number_format($recently['retail_price'], 2); ?></p>
                        <?php
                    }
                    if ($recently["destination_charge"]) {
                        echo "<sub>* Plus Applicable destination charge</sub>";
                    }
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <?php if (isset($master_class) && $master_class != ""): ?>
</div>
<?php endif; ?>
