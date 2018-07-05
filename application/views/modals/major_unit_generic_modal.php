<?php
$CI =& get_instance();

global $majorUnitGenericModal;

if (!isset($majorUnitGenericModal)) {
    $majorUnitGenericModal = false;
}

if (!$majorUnitGenericModal) {
    echo $CI->load->view("modals/major_unit_detail_modal", array(
            "motorcycle" => array()
    ), true);
    ?>
    <script type="application/javascript">
        $(document).ready(function () {
            // Show Major Unit Generic modal
            setTimeout(function () {
                var siteModalsState = JSON.parse(localStorage.getItem('siteModalsState')) || {};

                // If user has already seen the modal don't show it again
                if (siteModalsState['hasSeenGenericMajorUnitModal']) return;

                // If user has already made a form submission on another modal, don't show this modal
                if (siteModalsState['hasContactedSales']) return;

                // User hasn't seen modal yet so show it to them
                siteModalsState['hasSeenGenericMajorUnitModal'] = true;
                localStorage.setItem('siteModalsState', JSON.stringify(siteModalsState));
                $('.modal').modal('hide');

                // Fixes Bootstrap bug
                setTimeout(function () {
                    $('#major-unit-generic-modal').modal('show');
                }, 500);
            }, 5000);
        });
    </script>
    <?php
}