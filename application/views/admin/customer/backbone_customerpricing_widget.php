<?php
global $PSTAPI;
initializePSTAPI();
$distributors = $PSTAPI->distributor()->fetch();
$clean_distributors = array();
foreach ($distributors as $d) {
    if ($d->get("name") != "Lightspeed Feed") {
        $clean_distributors[] = $d;
    }
}
$distributors = $clean_distributors;
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
<?php if ($user_id > 0): ?>
<script type="text/template" id="CustomerTierTableView">


<p><strong>Apply Pricing Tiers</strong></p>

<p><em>If you set up default pricing rules for pricing tiers, you can then assign a customer to a specific pricing tier, and that customer will inherit all pricing rules associated with that pricing tier - e.g., Gold, Silver, Bronze.</em></p>

<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <td><b>Pricing Tier</b></td>
        <td><b>Action</b></td>
    </tr>
</thead>
<tbody>

</tbody>
</table>

</script>
<script type="text/template" id="CustomerTierRowView">
    <td><%= obj.name %></td>
    <td><a href="#" class="removeButton">Remove</a></td>
</script>
<script type="text/template" id="CustomerAddTierView">


<p><strong>Add Pricing Tier</strong></p>

<form>
<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <td><b>Pricing Tier</b></td>
        <td><b>Action</b></td>
    </tr>
    <tr>
        <td><select name='pricingtier_id'>
        <option value=''>-- Select Pricing Tier --</option>
        <?php
        // get the available options and print them here.
        $pricingtiers = $PSTAPI->pricingtier()->fetch();
        foreach ($pricingtiers as $pt) {
            ?>
            <option value="<?php echo $pt->id(); ?>"><?php echo $pt->get("name"); ?></option>
            <?php
        }
        ?>
        </select>
        </td>
        <td><a href="#" class="addButton">Add</a></td>
    </tr>
</thead>
<tbody>

</tbody>
</table>
</form>

</script>
<?php endif; ?>
<script type="text/template" id="CustomerPricingTableView">



<p><strong>Customer Pricing Rules</strong></p>

<?php if ($user_id > 0): ?>
<p><em>Please use the following to establish customer-specific pricing rules.</p>
<?php else: ?>
<p><em>Please use the following to establish default distributor-specific pricing rules that can then be assigned to one or more customers.</em></p>
<?php endif; ?>

<p><em>Rules:</em></p>
<ul>
<li> Cost+: Price will be set to a percentage above the cost, e.g. entering 10 will cause the price to be 110% of cost.</li>
<li> Retail-: Price will be set to a discount from retail, e.g. entering 10 will set the price to 90% of retail.</li>
<li> Margin %: Price will be set as a fraction of the margin between retail and cost, e.g. entering 70 will set the price to the cost plus 70% of margin (retail minus cost).</li>
</ul>

<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <?php if ($user_id == 0): ?>
        <td><b>Pricing Tier</b></td>
        <?php endif; ?>
        <td><b>Distributor</b></td>
        <td><b>Pricing Rule</b></td>
        <td><b>Percentage</b></td>
        <td><b>Action</b></td>
    </tr>
</thead>
<tbody>

</tbody>
</table>

</script>
<script type="text/template" id="CustomerPricingTableRowView">
    <?php if ($user_id == 0):?>
    <td><span class="editview"><input type="text" name="pricing_tier" size="40" maxlength="255" /></span><span class="noeditview"><%= obj.pricing_tier %></span></td>
    <?php endif; ?>
    <td><span class="editview"><select name="distributor_id"><option value="">All Distributors</option><?php foreach ($distributors as $d): ?><option value="<?php echo $d->get("distributor_id"); ?>"><?php echo $d->get("name"); ?></option><?php endforeach; ?></select></span><span class="noeditview"><%= obj.distributor_name %></span></td>
    <td><span class="editview"><select name="pricing_rule"><option value="Cost+">Cost+</option><option value="Retail-">Retail-</option><option value="PcntMgn">Margin %</option></select></span><span class="noeditview"><%= obj.pricing_rule %></span></td>
    <td><span class="editview"><input type="text" name="percentage" size="8" maxlength="8" /></span><span class="noeditview"><%= obj.percentage %>%</span></td>
    <td><span class="editview"><a href="#" class="updateButton">Update</a> <a href="#" class="cancelButton">Cancel</a></span><span class="noeditview"><a href="#" class="editButton">Edit</a> <a href="#" class="removeButton">Remove</a></span></td>
</script>
<script type="text/template" id="CustomerPricingAddView">

<p><strong>Add New Rule</strong></p>

<form>
<table width="100%" cellpadding="10" style="border:1px solid silver; border-collapse:separate;">
<thead>
    <tr class="head_row">
        <?php if ($user_id == 0): ?>
        <td><b>Pricing Tier</b></td>
        <?php endif; ?>
        <td><b>Distributor</b></td>
        <td><b>Pricing Rule</b></td>
        <td><b>Percentage</b></td>
        <td><b>Action</b></td>
    </tr>
    <tr>
        <?php if ($user_id == 0):?>
        <td><input type="text" name="pricing_tier" size="40" maxlength="255" /></td>
        <?php endif; ?>
        <td><select name="distributor_id"><option value="">All Distributors</option><?php foreach ($distributors as $d): ?><option value="<?php echo $d->get("distributor_id"); ?>"><?php echo $d->get("name"); ?></option><?php endforeach; ?></select></td>
        <td><select name="pricing_rule"><option value="Cost+">Cost+</option><option value="Retail-">Retail-</option><option value="PcntMgn">Margin %</option></select></td>
        <td><input type="text" name="percentage" size="8" maxlength="8" /></td>
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
<div class="pricetierholder"></div>
<div class="addpricetierholder"></div>
<script type="application/javascript">

    function showGritter(title, message) {
        $.extend($.gritter.options, {
            time: 1500 // hang on the screen for ...
        });

        setTimeout(function () {
            $.gritter.add({
                title: title,
                text: message,
                image: '',
                sticky: false,
                time: 5000
            });
            return false;
        }, 800);
    }


var current_user_id = <?php echo is_null($user_id) ? "null" : $user_id; ?>;

var myCustomerPricingAddView;
var myCustomerPricingTable;

<?php if ($user_id > 0): ?>
var myCustomerTierTableView;
var myCustomerAddTierView;

window.CustomerPricingTierModel = Backbone.Model.extend({
    defaults: {
        "customerpricingtier_id" : 0,
        "user_id" : 0,
        "pricingtier_id" : 0
    }
});

window.CustomerPricingTierCollection = Backbone.Collection.extend({
    model: CustomerPricingTierModel,
    comparator: function(x) {
        return x.get("name").toLowerCase();
    }
});

var myCustomerPricingTierCollection = new CustomerPricingTierCollection(
    <?php echo json_encode($PSTAPI->customerpricingtier()->fetch(array("user_id" => $user_id), true)); ?>
); 

<?php endif; ?>

window.CustomerPricingModel = Backbone.Model.extend({
    defaults: {
<?php if ($user_id == 0): ?>
        "pricing_tier" : "",
<?php endif; ?>
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
            <?php if ($user_id == 0): ?>
            if (a.get("pricing_tier") != b.get("pricing_tier")) {
                var a_tier = a.get("pricing_tier").toLowerCase();
                var b_tier = b.get("pricing_tier").toLowerCase();

                return a_name < b_name ? -1 : 1;
            }
            <?php endif; ?>

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

<?php if ($user_id > 0): ?>
window.CustomerTierRowView = Backbone.View.extend({
    template: _.template($("#CustomerTierRowView").text()),
    "className" : "CustomerTierRowView",
    "tagName" : "tr",
    initialize: function() {
        _.bindAll(this, "render", "removeButton");
    }, 
    "render" : function() {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },
    "events" : {
        "click .removeButton" : "removeButton"
    },
    "removeButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_tier_remove/" + this.model.get("customerpricingtier_id"),
            data: {
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    myCustomerPricingTierCollection.remove(this.model);
                    myCustomerTierTableView.redraw();
                } else {
                    showGritter("Error", response.error_message);
                }
            }, this),
            error: function() {
                alert("An error occurred; you may need to reload this page.");
            }
        });
    }
});

window.CustomerTierTableView = Backbone.View.extend({
    template: _.template($("#CustomerTierTableView").text()),
    "className" : "CustomerTierTableView",
    initialize: function() {
        _.bindAll(this, "render", "redraw");
    }, 
    "render" : function() {
        $(this.el).html(this.template({}));
        this.redraw();
        return this;
    },
    "redraw" : function() {
        if (myCustomerPricingTierCollection.length > 0) {
            this.$("table tbody").html("");

            for (var i = 0; i < myCustomerPricingTierCollection.length; i++) {
                var m = myCustomerPricingTierCollection.at(i);
                var v = new CustomerTierRowView({
                    model: m
                });
                this.$("table tbody").append(v.render().el);
            }

            this.$("table").show();
        } else {
            this.$("table").hide();
        }
    }
});

window.CustomerAddTierView = Backbone.View.extend({
    template: _.template($("#CustomerAddTierView").text()),
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

        var pricingtier_id = this.$("[name=pricingtier_id]").val();
        if (pricingtier_id == "" || pricingtier_id == 0) {
            pricingtier_id = null;
        }

        if (!pricingtier_id) {
            alert("Please select a tier.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_tier_add/<?php echo $user_id; ?>/" + pricingtier_id,
            data: {
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    myCustomerPricingTierCollection.push(response.data.model);
                    myCustomerPricingTierCollection.sort();
                    myCustomerTierTableView.redraw();
                } else {
                    showGritter("Error", response.error_message);
                }
            }, this),
            error: function() {
                alert("An error occurred; you may need to reload this page.");
            }
        });
    },
    "events" : {
        "click .addButton" : "addButton",
        "submit form" : "addButton"
    },
    "className" : "CustomerPricingAddView"
});
<?php endif; ?>

window.CustomerPricingTableRowView = Backbone.View.extend({
    template: _.template($("#CustomerPricingTableRowView").text()),
    "className" : "CustomerPricingTableRowView",
    "tagName" : "tr",
    initialize: function() {
        _.bindAll(this, "render", "cancelButton", "removeButton", "editButton", "updateButton");
    }, 
    "render" : function() {
        switch (this.model.get("pricing_rule")) {
            case 'Cost+':
            this.model.set("percentage", Math.round((parseFloat(this.model.get("amount")) - 1)*100, 2));
            break;
            
            case 'Retail-':
            this.model.set("percentage", Math.round((parseFloat(this.model.get("amount")) * 100), 2));
            break;
            
            case 'PcntMgn':
            this.model.set("percentage", Math.round((parseFloat(this.model.get("amount")) * 100), 2));                
            break;
        }
        $(this.el).html(this.template(this.model.toJSON()));
        this.cancelButton();
        return this;
    },
    "events" : {
        "click .cancelButton" : "cancelButton",
        "click .removeButton" : "removeButton",
        "click .editButton" : "editButton",
        "click .updateButton" : "updateButton"
    },
    "cancelButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        this.$(".editview").hide();
        this.$(".noeditview").show();
    },
    "removeButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_remove/" + this.model.get("customerpricing_id") <?php if ($user_id > 0) { echo "+ '/" . $user_id . "'"; } ?>,
            data: {
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    myCustomerPricingCollection.remove(this.model);
                    myCustomerPricingTable.redraw();
                } else {
                    showGritter("Error", response.error_message);
                }
            }, this),
            error: function() {
                alert("An error occurred; you may need to reload this page.");
            }
        });
    },
    "editButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        this.$("[name='percentage']").val(this.model.get("percentage"));
        this.$("select[name='distributor_id'] option[value='" + this.model.get('distributor_id') + "']").prop("selected", true);
        this.$("select[name='pricing_rule'] option[value='" + this.model.get('pricing_rule') + "']").prop("selected", true);
        <?php if ($user_id == 0): ?>
        this.$("[name='pricing_tier']").val(this.model.get("pricing_tier"));
        <?php endif; ?>

        this.$(".editview").show();
        this.$(".noeditview").hide();
    },
    "updateButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

                var distributor_id = this.$("[name=distributor_id]").val();
        if (distributor_id == "" || distributor_id == 0) {
            distributor_id = null;
        }

        var pricing_rule = this.$("[name='pricing_rule']").val();
        var percentage = this.$("[name='percentage']").val();

        if (!percentage) {
            alert("Please specify an percentage.");
            return;
        }

        <?php if ($user_id == 0): ?>
        var pricing_tier = this.$("[name='pricing_tier']").val();
        if (!pricing_tier || pricing_tier == "") {
            alert("Please name this pricing tier.");
            return;
        }
        <?php endif; ?>


        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_update/" + this.model.get("customerpricing_id") <?php if ($user_id > 0) { echo "+ '/" . $user_id . "'"; } ?>,
            data: {
                <?php if ($user_id == 0): ?>
                pricing_tier: pricing_tier,
                <?php endif; ?>
                distributor_id : distributor_id,
                percentage: percentage,
                pricing_rule : pricing_rule
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    myCustomerPricingCollection.remove(this.model);
                    myCustomerPricingCollection.push(response.data.model);
                    myCustomerPricingCollection.sort();
                    myCustomerPricingTable.redraw();
                } else {
                    showGritter("Error", response.error_message);
                }
            }, this),
            error: function() {
                alert("An error occurred; you may need to reload this page.");
            }
        });
    },
    "redraw" : function() {
        if (myCustomerPricingCollection.length > 0) {
            this.$("table tbody").html("");

            for (var i = 0; i < myCustomerPricingCollection.length; i++) {
                var m = myCustomerPricingCollection.at(i);
                var v = new CustomerPricingTableRowView({
                    model: m
                });
                this.$("table tbody").append(v.render().el);
            }

            this.$("table").show();
        } else {
            this.$("table").hide();
        }
    }
});

window.CustomerPricingTableView = Backbone.View.extend({
    template: _.template($("#CustomerPricingTableView").text()),
    "className" : "CustomerPricingTableView",
    initialize: function() {
        _.bindAll(this, "render", "redraw");
    }, 
    "render" : function() {
        $(this.el).html(this.template({}));
        this.redraw();
        return this;
    },
    "redraw" : function() {
        if (myCustomerPricingCollection.length > 0) {
            this.$("table tbody").html("");

            for (var i = 0; i < myCustomerPricingCollection.length; i++) {
                var m = myCustomerPricingCollection.at(i);
                var v = new CustomerPricingTableRowView({
                    model: m
                });
                this.$("table tbody").append(v.render().el);
            }

            this.$("table").show();
        } else {
            this.$("table").hide();
        }
    }
});

window.CustomerPricingAddView = Backbone.View.extend({
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

        var distributor_id = this.$("[name=distributor_id]").val();
        if (distributor_id == "" || distributor_id == 0) {
            distributor_id = null;
        }

        var pricing_rule = this.$("[name='pricing_rule']").val();
        var percentage = this.$("[name='percentage']").val();

        if (!percentage) {
            alert("Please specify an percentage.");
            return;
        }

        <?php if ($user_id == 0): ?>
        var pricing_tier = this.$("[name='pricing_tier']").val();
        if (!pricing_tier || pricing_tier == "") {
            alert("Please name this pricing tier.");
            return;
        }
        <?php endif; ?>

        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_add<?php if ($user_id > 0) { echo "/" . $user_id; } ?>",
            data: {
                <?php if ($user_id == 0): ?>
                pricing_tier: pricing_tier,
                <?php endif; ?>
                distributor_id : distributor_id,
                percentage: percentage,
                pricing_rule : pricing_rule
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    this.$("[name='percentage']").val("");
                    this.$("[name=distributor_id]").val("");
                    myCustomerPricingCollection.push(response.data.model);
                    myCustomerPricingCollection.sort();
                    myCustomerPricingTable.redraw();
                } else {
                    showGritter("Error", response.error_message);
                }
            }, this),
            error: function() {
                alert("An error occurred; you may need to reload this page.");
            }
        });
    },
    "events" : {
        "click .addButton" : "addButton",
        "submit form" : "addButton"
    },
    "className" : "CustomerPricingAddView"
})

$(document).on("ready", function() {
    myCustomerPricingTable = new CustomerPricingTableView({});
    myCustomerPricingAddView = new CustomerPricingAddView({});
    $(".addrowholder").html(myCustomerPricingAddView.render().el);
    $(".tableholder").html(myCustomerPricingTable.render().el);

    <?php if ($user_id > 0): ?>
    myCustomerTierTableView = new CustomerTierTableView({});
        myCustomerAddTierView = new CustomerAddTierView({});
    $(".addpricetierholder").html(myCustomerAddTierView.render().el);
    $(".pricetierholder").html(myCustomerTierTableView.render().el);
    <?php endif; ?>
});
</script>
