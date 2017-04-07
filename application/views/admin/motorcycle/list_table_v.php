<table width="100%" cellpadding="10">
    <tr class="head_row">
        <td><b>SKU</b></td>
        <td><b>Image</b></td>
        <td><b>Title</b></td>
        <td><b>Featured</b></td>
        <td><b>Active</b></td>
        <td><b>$ Sale Price</b></td>
        <td><b>Condition</b></td>
        <td><b>Mileage</b></td>
        <td><b>Action</b></td>
    </tr>

    <?php if (@$products): foreach ($products as $prod): ?>
            <tr>
                <td><?php echo $prod['sku']; ?></td>
                <td><img src="<?php echo $assets; ?>/images/test_image.jpg" width="30"></td>
                <td><?php echo $prod['title']; ?></td>
                <td><?php if ($prod['featured']): ?> YES <?php else: ?> NO <?php endif; ?></td>
                <td><?php if ($prod['status']): ?> YES <?php else: ?> NO <?php endif; ?></td>
                <td>
                    $<?php
                        echo $prod['sale_price'];
                    ?></td>
                <td><?php echo $prod['condition'] == '1' ? 'New' : 'Used' ; ?></td>
                <td>
                    <?php
						echo $prod['mileage'];
					?>
                </td>
                <td>
                    <a href="<?php echo base_url('admin/motorcycle_edit/' . $prod['id']); ?>"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>
        <?php if (!$prod['mx']): ?>  | <a href="<?php echo base_url('admin/motorcycle_delete/' . $prod['id']);?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a> <?php endif; ?>
                </td>
            </tr>
		<?php endforeach;
	endif; ?>
</table>
