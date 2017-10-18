<?php
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

switch (ctd) {

<?php
foreach ($active_primary_navigation as $rec): ?>
    <?php if ($rec['category_description'] != ''): ?>
    case '<?php echo $rec['category_description']; ?>':
    resetAll();
    $("#<?php echo $rec['span_id']; ?>").addClass('actv');
    break;
    <?php endif; ?>
<?php endforeach; ?>

}