<?php
global $PSTAPI;
initializePSTAPI();
$distributors = $PSTAPI->distributor()->fetch();
usort($distributors, function($a, $b) {
    return strnatcasecmp($a->get("name"), $b->get("name"));
});
?>
<!-- Gritter -->
<link rel="stylesheet"
        href="/assets/Gritter/css/jquery.gritter.css" />
<!--<link rel="stylesheet" href="/assets/newjs/jquery-ui.structure.min.css" />-->
<link rel="stylesheet" href="/assets/newjs/jquery-ui.min.css" />

<script type="text/javascript"
        src="/assets/Gritter/js/jquery.gritter.min.js"></script>

<script type="application/javascript" src="/assets/underscore/underscore-min.js" ></script>
<script type="application/javascript" src="/assets/backbone/backbone-min.js" ></script>
<script type="application/javascript" src="/assets/dropzone/dropzone.js" ></script>
<script type="application/javascript" src="/assets/newjs/jquery-ui.min.js" ></script>
<script type="text/template" id="CustomerPricingTableView">

<p><strong>Customer Pricing Rules</strong></p>

<?php if ($user_id > 0): ?>
<p><em>Please use the following to establish customer-specific pricing rules.</p>
<?php else: ?>
<p><em>Please use the following to establish default distributor-specific pricing rules.</em></p>
<?php endif; ?>

<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <td><b>Distributor</b></td>
        <td><b>Pricing Rule</b></td>
        <td><b>Multiplier</b></td>
        <td><b>Action</b></td>
    </tr>
</thead>
<tbody>

</tbody>
</table>

</script>
<script type="text/template" id="CustomerPricingTableRowViw">

</script>
<script type="text/template" id="CustomerPricingAddView">

<p><strong>Add New Rule</strong></p>

<form>
<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <td><b>Distributor</b></td>
        <td><b>Pricing Rule</b></td>
        <td><b>Multiplier</b></td>
        <td><b>Action</b></td>
    </tr>
    <tr>
        <td><select name="distributor_id"><option value="">Default</option><?php foreach ($distributors as $d): ?><option value="<?php echo $d->get("distributor_id"); ?>"><?php echo $d->get("name"); ?></option><?php endforeach; ?></select></td>
        <td><select name="pricing_rule"><option value="Cost+">Cost+</option><option value="Retail-">Retail-</option><option value="PcntMgn">Margin %</option></select></td>
        <td><input type="text" name="amount" size="8" maxlength="8" /></td>
        <td><a href="#" class="addButton">Add</a></td>
    </tr>
</thead>
<tbody>

</tbody>
</table>
</form>

</script>
<div class="tableholder"></div>
<div class="addrowholder"></div>
<script type="application/javascript">
var current_user_id = <?php echo is_null($user_id) ? "null" : $user_id; ?>;

window.CustomerPricingModel = Backbone.Model.extend({
    defaults: {
        "customerpricing_id" : 0,
        "distributor_id" : 0,
        "distributor_name" : "default",
        "user_id" : current_user_id,
        "pricing_rule" : "Cost+",
        "amount" : 0
    },
    "idAttribute" : "customerpricing_id"
});
window.CustomerPricingCollection = Backbone.Collection.extend({
    model: CustomerPricingModel,
        comparator: function(a, b) {
            if (a.get("distributor_id") > 0 && b.get("distributor_id") > 0) {
                var a_name = a.get("distributor_name").toLowerCase();
                var b_name = b.get("distributor_name").toLowerCase();

                if (a_name < b_name) {
                    return -1;                    
                } else if (a_name > b_name) {
                    return 1;
                } else {
                    return 0;
                }

            } else if (a.get("distributor_id") > 0) {
                return 1;
            } else if (b.get("distributor_id") > 0) {
                return -1;
            } else {
                return 0;
            }
        }
});

// instantiate the pricing collection
var myCustomerPricingCollection = new CustomerPricingCollection(<?php echo json_encode($PSTAPI->customerpricing()->fetchFrontEnd($user_id)); ?>);

window.CustomerPricingAddView = new Backbone.View.extend({
    template: _.template($("#CustomerPricingAddView").text()),
    initialize: function() {
        _.bindAll(this, "render", "addButton");
    }, 
    "render" : function() {
        $(this.el).html(this.template({}));
        return this;
    },
    "addButton" : function(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
    },
    "events" : {
        "click .addButton" : "addButton"
    },
    "className" : "CustomerPricingAddView"
})

var myCustomerPricingAddView;

$(document).on("ready", function() {
    myCustomerPricingAddView = new CustomerPricingAddView({});
    $(".addrowholder").html(myCustomerPricingAddView.render().el);
});
</script>
