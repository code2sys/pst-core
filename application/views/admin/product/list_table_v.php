<table width="100%" cellpadding="10" id="admin_product_list_table_v">
    <thead>
        <tr>
            <th><b>Product Code</b></th>
            <th><b>Image</b></th>
            <th><b>Title</b></th>
            <th><b>Dealer Only?</b></th>
            <th><b>Featured</b></th>
            <th><b>$ Cost</b></th>
            <th><b>$ Retail</b></th>
            <th><b>$ Markup</b></th>
            <th><b>$ Sale</b></th>
            <th><b>Action</b></th>
        </tr>
    </thead>
    <tbody>
    <?php if (@$products): foreach ($products as $prod): ?>
            <tr>
                <td><?php echo $prod['partnumber']; ?></td>
                <td><img src="<?php echo $assets; ?>/images/test_image.jpg" width="30"></td>
                <td><?php echo $prod['name']; ?></td>
                <td><?php if ($prod['featured']): ?> YES <?php else: ?> NO <?php endif; ?></td>
                <td>
                    $<?php
                    if ($prod['cost_min'] == $prod['cost_max']):
                        echo $prod['cost_min'];
                    else:
                        echo $prod['cost_min'];
                        ?> - $<?php echo $prod['cost_max'];
        endif;
        ?></td>
                <td>
                    $<?php
                    if ($prod['price_min'] == $prod['price_max']):
                        echo $prod['price_min'];
                    else:
                        echo $prod['price_min'];
                        ?> - $<?php echo $prod['price_max'];
                    endif;
                    ?></td>
                <td><?php echo $prod['markup']; ?>%</td>
                <td>
                    $<?php
            if ($prod['sale_min'] == $prod['sale_max']):
                echo $prod['sale_min'];
            else:
                echo $prod['sale_min'];
                ?> - $<?php echo $prod['sale_max'];
            endif;
            ?>
                </td>
                <td>
                    <a href="<?php echo base_url('admin/product_edit/' . $prod['part_id']); ?>"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>
        <?php if (!$prod['mx']): ?>  | <a href=""><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a> <?php endif; ?>
                </td>
            </tr>
    <?php endforeach;
endif; ?>
    </tbody>
</table>
