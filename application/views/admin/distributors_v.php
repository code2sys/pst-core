<!-- CONTENT -->
<div class="content">


    <!-- ADMIN SECTION -->
    <div style="width:95.6%;">

        <h1>Distributors</h1>

        <!-- PHP ALERT -->
        <?php if($error != ""): ?>
            <div class="validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <?php echo $error; ?>
            </div>
            <br />
        <?php endif; ?>
        <div class="clear"></div>

        <?php if(@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div class="clear"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>


        <h2>Add Distributor</h2>

        <form method="post" action="<?php echo site_url('admindistributors/add_distributor'); ?>">
            <label>
                <strong>Name</strong> <input type="text" name="name" class="text large" size="40" maxlength="255" /></label></td>
<br/><button type="submit">Add Distributor</button>
        </form>
        <!-- DISTRIBUTORS -->
        <br/>&nbsp;<br/>

        <h2>Distributor Accounts</h2>

        <div class="tabular_data">
            <table cellpadding="3" style="width:100%;">
                <tr class="head_row">
                    <td><b>Name</b></td>
                    <td><b>Dealer Number</b></td>
                    <td><b>Username</b></td>
                    <td><b>Password</b></td>
                    <td><b>Actions</b></td>
                </tr>
                <?php if(@$distributors): foreach($distributors as $dis): ?>
                    <tr>
                        <td>
                            <form action="<?php echo base_url('admindistributors/update_distributor/' . $dis['distributor_id']); ?>" method="post" class="form_standard">
                                <?php if ($dis['customer_distributor'] > 0): ?>
                                <input type="text" name="name" maxlength="255" value="<?php echo htmlentities($dis['name']); ?>" class="text large"/>
                        <?php else:?>
                                <?php echo $dis['name']; ?>
                        <?php endif; ?>
                        </td>
                        <?php foreach (array("dealer_number", "username", "password") as $key): ?>
                        <td><input type="text" maxlength="64" name="<?php echo $key; ?>" value="<?php echo htmlentities($dis[$key]); ?>" class="text large" /></td>
                        <?php endforeach; ?>

                        <td><button type="submit">Update</button></form><?php if ($dis['customer_distributor'] > 0): ?>| <a href="<?php echo site_url("admindistributors/remove_distributor/" . $dis["distributor_id"]); ?>" onclick="return confirm('Really delete? This cannot be undone.');"><i class="fa fa-times"></i>&nbsp; Delete<?php endif; ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </table>
        </div>

    </div>
</div>