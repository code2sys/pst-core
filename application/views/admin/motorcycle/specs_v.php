<?php
$cstdata = (array) json_decode($product['data']);
?>
<!-- Gritter -->
<link rel="stylesheet"
      href="https://portal.powersporttechnologies.com/smashing/theme/scripts/Gritter/css/jquery.gritter.css" />
<!--<link rel="stylesheet" href="/assets/newjs/jquery-ui.structure.min.css" />-->
<link rel="stylesheet" href="/assets/newjs/jquery-ui.min.css" />

<script type="text/javascript"
        src="https://portal.powersporttechnologies.com/smashing/theme/scripts/Gritter/js/jquery.gritter.min.js"></script>

<script type="application/javascript" src="https://portal.powersporttechnologies.com/thirdparty/underscore/underscore-min.js" ></script>
<script type="application/javascript" src="https://portal.powersporttechnologies.com/thirdparty/backbone/backbone-min.js" ></script>
<script type="application/javascript" src="https://portal.powersporttechnologies.com/thirdparty/dropzone/dropzone.js" ></script>
<script type="application/javascript" src="/assets/newjs/jquery-ui.min.js" ></script>
<script type="text/template" id="SpecGroupView">
    <div style="float: right">
        <a href="#" class="edit-specgroup-button"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
        <a href="#" class="remove-specgroup-button"><i class="fa fa-trash-o"></i>&nbsp;Remove</a>
    </div>
    <div style="float: left; margin-right: 0.5em">
        <a href="#" class="drag-drop-specgroup-button"><i class="fa fa-arrows-v"></i></a>
    </div>

    <div class="preview-specgroup">
    <%= obj.name %>
    </div>
    <div class="edit-specgroup">

    </div>

    <div style="clear: both">
    </div>

    <div class="spec-holder"></div>

</script>
<script type="text/template" id="SpecView">
    <div style="float: right">
        <a href="#" class="edit-spec-button"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
        <a href="#" class="remove-spec-button"><i class="fa fa-trash-o"></i>&nbsp;Remove</a>
    </div>
    <div style="float: left; margin-right: 0.5em;">
        <a href="#" class="drag-drop-spec-button"><i class="fa fa-arrows-v"></i></a>
    </div>

    <div class="preview">
        <div class="label"><%= obj.feature_name %><% if (obj.attribute_name && obj.attribute_name != '') { %> - <%= obj.attribute_name %><% } %></div>
        <div class="value"><%= obj.final_value %></div>
    </div>
    <div class="edit">

    </div>

    <div style="clear: both"></div>

</script>
<script type="text/template" id="SpecGroupsView">

<div class="holder"></div>
</script>
<script type="text/template" id="AddSpecGroupView">
<div style="padding: 2em">
    <a href="#" class="add-spec-group-button"><i class="fa fa-plus"></i>&nbsp;Add a new Functional Group</a>
</div>
</script>
<script type="application/javascript">

    window.SpecModel = Backbone.Model.extend({

    });

    window.SpecCollection = Backbone.Collection.extend({
        model: SpecModel,
        comparator: function(x) {
            return parseInt(x.get("ordinal"), 10);
        }
    });

    window.SpecGroupModel = Backbone.Model.extend({

    });

    window.SpecGroupCollection = Backbone.Collection.extend({
        model: SpecGroupModel,
        comparator: function(x) {
            return parseInt(x.get("ordinal"), 10);
        }
    });

    // now, instantiate these into what we have now....
    var mySpecCollection = new SpecCollection(<?php echo json_encode($specs); ?>);
    var mySpecGroupCollection = new SpecGroupCollection(<?php echo json_encode($specgroups); ?>);

    // create a LUT of matches
    var mySpecGroupLUT = {};
    for (var i = 0; i < mySpecCollection.length; i++) {
        var m = mySpecCollection.at(i);
        var motorcyclespecgroup_id = m.get("motorcyclespecgroup_id");
        if (!mySpecGroupLUT[motorcyclespecgroup_id]) {
            mySpecGroupLUT[motorcyclespecgroup_id] = [];
        }
        mySpecGroupLUT[motorcyclespecgroup_id].push(m);
    }

    // now, we have to instantiate our views....
    window.SpecGroupView = Backbone.View.extend({
        className: "SpecGroupView",
        template: _.template($("#SpecGroupView").html()),
        events: {
        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "subrender");
        },
        "subrender" : function() {
            this.$(".spec-holder").html("");
            // we have to stamp them out...
            var motorcyclespecgroup_id = this.model.get("motorcyclespecgroup_id");
            if (mySpecGroupLUT[motorcyclespecgroup_id]) {
                for (var i = 0; i< mySpecGroupLUT[motorcyclespecgroup_id].length; i++) {
                    this.$(".spec-holder").append(new SpecView({
                        model: mySpecGroupLUT[motorcyclespecgroup_id][i]
                    }).render().el);
                }
            }
        },
        "render" : function() {
            $(this.el).html(this.template(this.model.toJSON()));
            this.subrender();
            return this;
        }
    });

    window.SpecGroupsView = Backbone.View.extend({
        className: "SpecGroupsView",
        template: _.template($("#SpecGroupsView").html()),
        events: {
        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "subrender");
        },
        subrender: function() {
            this.$(".holder").html("");
            for (var i = 0; i < mySpecGroupCollection.length; i++) {
                this.$(".holder").append(new SpecGroupView({
                    model: mySpecGroupCollection.at(i)
                }).render().el);
            }
        },
        "render" : function() {
            $(this.el).html(this.template({}));
            this.subrender();
            return this;
        }
    });

    window.SpecView = Backbone.View.extend({
        className: "SpecView",
        template: _.template($("#SpecView").html()),
        events: {
        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render");
        },
        "render" : function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        }
    });

    window.AddSpecGroupView = Backbone.View.extend({
        className: "AddSpecGroupView",
        template: _.template($("#AddSpecGroupView").html()),
        events: {
            "click .add-spec-group-button" : "addspecgroup"
        },
        "addspecgroup" : function(e) {
            e.stopPropagation();
            e.preventDefault();

            // OK, we're going to make an ajax call
        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render");
        },
        "render" : function() {
            $(this.el).html(this.template({}));
            return this;
        }
    });


    // Finally, what fires this wholt hing off??
    $(document).on("ready", function() {
        // we have to instantiate
        var mySpecGroupsView = new SpecGroupsView();
        var myAddSpecGroupView = new AddSpecGroupView({
            "specgroups" : mySpecGroupsView
        });
        $("#collection_holder").html(mySpecGroupsView.render().el);
        $("#button_holder").html(myAddSpecGroupView.render().el);
    });


</script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/motorcycle/moto_head", array(
            "new" => @$new,
            "product" => @$product,
            "success" => @$success,
            "assets" => $assets,
            "id" => @$id,
            "active" => "edit",
            "descriptor" => "Specifications Options"
        ), true);

        ?>

        <!-- END TABS -->
        <?php echo form_open('admin/update_motorcycle/' . $id, array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">

            // instructions
            <div class="instructions">
                <p>
                    Here, you can add, edit, remove, and re-order specifications for this unit in your inventory. Specifications are displayed in functional groups; you can re-order functional groups of specifications as well.
                </p>
            </div>

            <div id="collection_holder"></div>
            <div id="button_holder"></div>
        </div>
        <!-- END TAB CONTENT -->
        <br>


        <!-- SUBMIT DISABLED
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>

        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->




    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<style>
    .small-hndr {width:100px !important;}
    .frst {margin-left: 55px !important;}
    .inr-td {width:200px;}

    .SpecGroupView {
        border: 1px solid gray;
        padding: 1em;
        margin-bottom: 0.5em;

    }

    .SpecGroupView .preview-specgroup,
    .SpecGroupView .edit-specgroup {
        margin-left: 0.25em; margin-right: 0.25em;
    }

    .SpecGroupView .spec-holder {
        border: 1px solid gray;
        margin: 1em;
    }

    .SpecGroupView .SpecView:nth-child(odd) {
        background-color: #dddddd;
        border-top: 1px solid gray;
        border-bottom: 1px solid gray;
    }

    .SpecView {
        padding: 1em;
    }

    .SpecView .label {
        font-weight: bold;
        float: left;
        max-width: 50%;
    }

    .SpecView .value {
        float: right;
        max-width: 50%;
        padding-right: 1.5em;
    }
</style>
