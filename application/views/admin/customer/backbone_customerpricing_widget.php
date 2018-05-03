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
<script type="text/template" id="CustomerPricingTableRowView">
    <td><span class="editview"></span><span class="noeditview"><%= obj.distributor_name %></span></td>
    <td><span class="editview"></span><span class="noeditview"><%= obj.pricing_rule %></span></td>
    <td><span class="editview"></span><span class="noeditview"><%= obj.amount %></span></td>
    <td><span class="editview"><a href="#" class="updateButton">Update</a> <a href="#" class="cancelButton">Cancel</a></span><span class="noeditview"><a href="#" class="editButton">Edit</a> <a href="#" class="removeButton">Remove</a></span></td>
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

window.CustomerPricingTableRowView = Backbone.View.extend({
    template: _.template($("#CustomerPricingTableRowView").text()),
    "className" : "CustomerPricingTableRowView",
    "tagName" : "tr",
    initialize: function() {
        _.bindAll(this, "render", "cancelButton", "removeButton", "editButton", "updateButton");
    }, 
    "render" : function() {
        $(this.el).html(this.template(this.model.toJSON()));
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
    },
    "editButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.$(".editview").show();
        this.$(".noeditview").hide();
    },
    "updateButton" : function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
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
        var amount = this.$("[name='amount']").val();

        if (!amount) {
            alert("Please specify an amount.");
            return;
        }


        $.ajax({
            type: "POST",
            url: "/admin/ajax_customer_pricing_add<?php if ($user_id > 0) { echo "/" . $user_id; } ?>",
            data: {
                distributor_id : distributor_id,
                amount: amount,
                pricing_rule : pricing_rule
            },
            dataType: "json",
            success : _.bind(function(response) {
                console.log(response);
                if (response.success) {
                    showGritter("Success", response.success_message);
                    this.$("[name='amount']").val("");
                    this.$("[name=distributor_id]").val("");
                    myCustomerPricingCollection.push(response.data.model);
                    CustomerPricingTableView.redraw();
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
        "click .addButton" : "addButton"
    },
    "className" : "CustomerPricingAddView"
})

var myCustomerPricingAddView;
var myCustomerPricingTable;

$(document).on("ready", function() {
    myCustomerPricingTable = new CustomerPricingTableView({});
    myCustomerPricingAddView = new CustomerPricingAddView({});
    $(".addrowholder").html(myCustomerPricingAddView.render().el);
    $(".tableholder").html(myCustomerPricingTable.render().el);
});
</script>
