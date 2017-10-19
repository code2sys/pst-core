<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/18/17
 * Time: 5:40 PM
 *
 * I have no clue why selector2 exists. It looks like stupid, repeated code.
 *
 */


$CI =& get_instance();
global $active_primary_navigation;

if (!isset($active_primary_navigation)) {
    $active_primary_navigation = jonathan_prepareGlobalPrimaryNavigation();
}

?>
var resetAll = function() {
<?php
foreach ($active_primary_navigation as $rec): ?>
    $("#<?php echo $rec['span_id']; ?>").removeClass('actv');
<?php endforeach; ?>
}

switch (ct) {

<?php
foreach ($active_primary_navigation as $rec): ?>
    <?php if ($rec['category_id'] != ''): ?>
        case '<?php echo $rec['category_id']; ?>':
        resetAll();
        $("#<?php echo $rec['span_id']; ?>").addClass('actv');
        break;
    <?php endif; ?>
<?php endforeach; ?>

}