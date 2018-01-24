<?php
					$retail_price_zero = $motorcycle["retail_price"] === "" || is_null($motorcycle["retail_price"]) || ($motorcycle["retail_price"] == "0.00") || ($motorcycle["retail_price"] == 0) || (floatVal($motorcycle["retail_price"]) < 0.01);
					$sale_price_zero = $motorcycle["sale_price"] === "" || is_null($motorcycle["sale_price"]) || ($motorcycle["sale_price"] == "0.00") || ($motorcycle["sale_price"] == 0) || (floatVal($motorcycle["sale_price"]) < 0.01);
				?>
                <?php if( $motorcycle['call_on_price'] == '1' ||  ($retail_price_zero && $sale_price_zero) ) { ?>
                    <p class="cfp">Call For Price</p>
                <?php } else {
                    if (!$sale_price_zero && $motorcycle["sale_price"] != $motorcycle["retail_price"]) { ?>
                        <?php if (!$retail_price_zero): ?>
                            <p>Retail Price: &nbsp; <span class="strikethrough">$<?php echo $motorcycle['retail_price'];?></span></p>
                        <?php endif; ?>
                        <p>Sale Price: &nbsp; &nbsp;<span class="redtext">$<?php echo $motorcycle['sale_price'];?></span></p>
                    <?php } else { ?>
                        <p>Retail Price: &nbsp; $<?php echo $motorcycle['retail_price'];?></p>
                        <?php
                    }
                    if ($motorcycle["destination_charge"]) {
                        echo "<sub>* Plus Applicable destination charge</sub>";
                    }
                } ?>