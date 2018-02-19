<?php
// Are there any answers?
$answers = false;

foreach ($partvariations as $p) {
    if (trim($p["answer"]) != "") {
        $answers = true;
    }
}
?>
<div id="part_ajax_getActive_partnumbers">
    <table>
        <thead>
        <tr>
            <?php if ($answers): ?>
                <th>Product Option</th>
            <?php endif; ?>
            <th>Distributor Part #</th>
            <th>Manufacturer Part #</th>
            <th>Stock Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($partvariations as $p): ?>
            <tr>
                <?php if ($answers): ?>
                <td valign="top"><?php echo $p["answer"]; ?></td>
                <?php endif; ?>
                <td valign="top"><?php echo $p["part_number"]; ?></td>
                <td valign="top"><?php echo $p["manufacturer_part_number"]; ?></td>
                <td valign="top">
                    <?php
                    if ($p["in_stock"] > 0): ?>
                        In Stock
                    <?php else: ?>
                        Out Of Stock
                        <?php endif; ?>

                    <?php if ($p["stock_code"] == "Closeout"): ?>
                    - Closeout
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>


</div>
