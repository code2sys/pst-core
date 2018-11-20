<?php
global $PSTAPI;
initializePSTAPI();

if ($title != "") {
    ?>
    <h1><?php echo $title; ?></h1>
    <?php
}

if ($full_url != "") {
    // we have to generate the parts.
    ?>
    <nav class="breadcrumb showcasebreadcrumb">
				<a href="<?php echo site_url(""); ?>">Home</a>
				<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
				<a href="<?php echo site_url("Factory_Showroom"); ?>">Factory Showroom</a>
        <?php
        $pieces = explode("/", $full_url);

        for ($i = 0; $i < count($pieces); $i++) {
            $piece = $pieces[$i];

            $factory = "showcase";
            switch ($i) {
                case 0:
                    $factory .= "make";
                    break;

                case 1:
                    $factory .= "machinetype";
                    break;

                case 2:
                    $factory .= "model";
                    break;

                case 3:
                    $factory .= "trim";
                    break;
            }

            $object = $PSTAPI->$factory()->fetch(array(
                "url_title" => $piece
            ));

            if (count($object) > 0) {
                $object = $object[0];
                ?>
        <span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                <a href="<?php echo site_url("Factory_Showroom/" . $object->get("full_url")); ?>"><?php echo $object->get("title"); ?></a>
            <?php
            }
        }

        ?>
			</nav>
    <?php
}