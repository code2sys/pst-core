<?php
$cstdata = (array) json_decode($product['data']);
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
    <form>
        <strong>Group Title: </strong> <input type="text" name="name" size="60" maxlength="128" /><br/>
        <button class="save-specgroup-button btn-primary" type="button">Save</button>
        <button class="cancel-specgroup-button btn-default" type="button">Cancel</button>
    </form>
    </div>

    <div style="clear: both">
    </div>

    <div class="spec-holder"></div>

    <div style="text-align: right">
        <a href="#" class="add-spec-button"><i class="fa fa-plus"></i>&nbsp;Add Spec</a>
    </div>

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
        <strong>Label: </strong> <input type="text" name="feature_name" size="60" maxlength="128" /><br/>
        <strong>Optional Additional Label: </strong> <input type="text" name="attribute_name" size="60" maxlength="128" /><br/>
        <strong>Value: </strong> <input type="text" name="final_value" size="60" maxlength="1024" /><br/>
        <button class="save-spec-button btn-primary" type="button">Save</button>
        <button class="cancel-spec-button btn-default" type="button">Cancel</button>
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


    window.SpecModel = Backbone.Model.extend({
        defaults : {
            "motorcyclespec_id" : 0,
            "created": "",
            "version_number" : "",
            "final_value" : "",
            "feature_name" : "",
            "attribute_name" : ""
        }
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
            "click .remove-specgroup-button" : "removeSpecgroupButton",
            "click .add-spec-button" : "addSpecButton",
            "submit form" : "emptyAction",
            "click .edit-specgroup-button" : "showEditForm",
            "click .save-specgroup-button" : "saveButton",
            "click .cancel-specgroup-button" : "cancelButton"
        },

        "emptyAction" : function(e) {
            e.stopPropagation();
            e.preventDefault();
        },
        "showEditForm" : function(e) {
            e.stopPropagation();
            e.preventDefault();

            this.$(".preview-specgroup").hide();
            this.$(".edit-specgroup").show();
            this.$(".edit-specgroup-button").hide();
            this.$("input[name=name]").val(this.model.get("name"));
        },
        "saveButton" : function(e) {
            e.stopPropagation();
            e.preventDefault();

            // we need to save it, then we have to update the model and the preview, then we have to pretend we pressed the cancel button.
// OK, we're going to make an ajax call
            $.ajax({
                "url" : "/admin/ajax_motorcycle_specgroup_update/<?php echo $id; ?>/" + this.model.get("motorcyclespecgroup_id"),
                "type" : "POST",
                "dataType" : "json",
                "data": {
                    "name" : this.$("input[name=name]").val()
                },
                "success" : _.bind(function(data) {
                    if (data.success) {
                        // we have to add a new one...
                        showGritter("Success", "Title updated successfully.");
                        this.model.set("name", data.data.name);
                        this.cancelButton();
                    } else {
                        // error...
                        showGritter("Error", data.error_message);
                    }
                }, this)
            });
        },
        "cancelButton" : function(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            this.$(".preview-specgroup").html(this.model.get("name"));
            this.$(".preview-specgroup").show();
            this.$(".edit-specgroup").hide();
            this.$(".edit-specgroup-button").show();
        },
        "addSpecButton" : function(e) {
            e.preventDefault();
            e.stopPropagation();

            $.ajax({
                "url" : "/admin/ajax_motorcycle_specgroup_addspec/<?php echo $id; ?>/" + this.model.get("motorcyclespecgroup_id"),
                "type" : "POST",
                "dataType" : "json",
                "data": {
                },
                "success" : _.bind(function(data) {
                    if (data.success) {
                        // we have to add a new one...
                        var m = new SpecModel(data.data.model);
                        this.$(".spec-holder").append(new SpecView({
                            model: m
                        }).render().el);

                    } else {
                        // error...
                        showGritter("Error", data.error_message);
                    }
                }, this)
            });
        },
        "removeSpecgroupButton" : function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (confirm("Remove this entire functional group?")) {

                $.ajax({
                    "url" : "/admin/ajax_motorcycle_specgroup_remove/<?php echo $id; ?>/" + this.model.get("motorcyclespecgroup_id"),
                    "type" : "POST",
                    "dataType" : "json",
                    "data": {
                    },
                    "success" : _.bind(function(data) {
                        if (data.success) {
                            showGritter("Success", "Removed successfully");
                            $(this.el).remove(); // clear it out...
                        } else {
                            // error...
                            showGritter("Error", data.error_message);
                        }
                    }, this)
                });
            }

        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "subrender", "removeSpecgroupButton", "emptyAction", "showEditForm", "saveButton", "cancelButton");
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

                this.$(".spec-holder").sortable({
                    placeholder: "ui-state-highlight",
                    handle: ".drag-drop-spec-button"
                }).on("sortstop", _.bind(function(event, ui) {
                    /*
                    The idea here is that we're going to get the IDs in order of the things under this, then we'll shove that up. That will create new in-order ordinals.
                     */
                    var new_order = [];

                    this.$(".SpecView").each(function() {
                        new_order.push($(this).attr("data-motorcyclespec-id"));
                    });

                    if (new_order.length > 0) {
                        // now, blow your mind, post toastee.
                        $.ajax({
                            "url" : "/admin/ajax_motorcycle_specs_reorder/<?php echo $id; ?>/" + this.model.get("motorcyclespecgroup_id"),
                            type: "POST",
                            dataType: "json",
                            data: {
                                "new_order" : new_order
                            },
                            success: _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", "Order updated.");
                                } else {
                                    showGritter("Error", "Sorry, change failed. Please refresh and try again.");
                                }
                            }, this)
                        });
                    }

                }, this));

            }
        },
        "render" : function() {
            $(this.el).html(this.template(this.model.toJSON()));
            this.subrender();
            this.cancelButton();
            $(this.el).attr("data-motorcyclespecgroup-id", this.model.get("motorcyclespecgroup_id"));
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
            _.bindAll(this, "render", "subrender", "addOne");
        },
        subrender: function() {
            this.$(".holder").html("");
            for (var i = 0; i < mySpecGroupCollection.length; i++) {
                this.$(".holder").append(new SpecGroupView({
                    model: mySpecGroupCollection.at(i)
                }).render().el);
            }

            this.$(".holder").sortable({
                placeholder: "ui-state-highlight",
                handle: ".drag-drop-specgroup-button"
            }).on("sortstop", _.bind(function(event, ui) {
                /*
                The idea here is that we're going to get the IDs in order of the things under this, then we'll shove that up. That will create new in-order ordinals.
                 */
                var new_order = [];

                this.$(".SpecGroupView").each(function() {
                    new_order.push($(this).attr("data-motorcyclespecgroup-id"));
                });

                if (new_order.length > 0) {
                    // now, blow your mind, post toastee.
                    $.ajax({
                        "url" : "/admin/ajax_motorcycle_specgroups_reorder/<?php echo $id; ?>",
                        type: "POST",
                        dataType: "json",
                        data: {
                            "new_order" : new_order
                        },
                        success: _.bind(function(data) {
                            if (data.success) {
                                showGritter("Success", "Order updated.");
                            } else {
                                showGritter("Error", "Sorry, change failed. Please refresh and try again.");
                            }
                        }, this)
                    });
                }

            }, this));

        },
        "render" : function() {
            $(this.el).html(this.template({}));
            this.subrender();
            return this;
        },
        "addOne" : function(m) {
            this.$(".holder").append(new SpecGroupView({
                model: m
            }).render().el);
        }
    });

    window.SpecView = Backbone.View.extend({
        className: "SpecView",
        template: _.template($("#SpecView").html()),
        events: {
            "click .remove-spec-button" : "removeSpecButton",
            "submit form" : "emptyAction",
            "click .edit-spec-button" : "showEditForm",
            "click .save-spec-button" : "saveButton",
            "click .cancel-spec-button" : "cancelButton"
        },

        "emptyAction" : function(e) {
            e.stopPropagation();
            e.preventDefault();
        },
        "showEditForm" : function(e) {
            e.stopPropagation();
            e.preventDefault();

            this.$(".preview").hide();
            this.$(".edit").show();
            this.$(".edit-spec-button").hide();
            this.$("input[name=final_value]").val(this.model.get("final_value"));
            this.$("input[name=attribute_name]").val(this.model.get("attribute_name"));
            this.$("input[name=feature_name]").val(this.model.get("feature_name"));
        },
        "saveButton" : function(e) {
            e.stopPropagation();
            e.preventDefault();

            // we need to save it, then we have to update the model and the preview, then we have to pretend we pressed the cancel button.
// OK, we're going to make an ajax call
            $.ajax({
                "url" : "/admin/ajax_motorcycle_spec_update/<?php echo $id; ?>/" + this.model.get("motorcyclespecgroup_id") + "/" + this.model.get("motorcyclespec_id"),
                "type" : "POST",
                "dataType" : "json",
                "data": {
                    "feature_name" : this.$("input[name=feature_name]").val(),
                    "attribute_name" : this.$("input[name=attribute_name]").val(),
                    "final_value" : this.$("input[name=final_value]").val()
                },
                "success" : _.bind(function(data) {
                    if (data.success) {
                        // we have to add a new one...
                        showGritter("Success", "Spec updated successfully.");
                        this.model.set("feature_name", data.data.feature_name);
                        this.model.set("attribute_name", data.data.attribute_name);
                        this.model.set("final_value", data.data.final_value);
                        this.cancelButton();
                    } else {
                        // error...
                        showGritter("Error", data.error_message);
                    }
                }, this)
            });
        },
        "cancelButton" : function(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            // we have to fill in the name and the value...
            this.$(".preview .value").html(this.model.get("final_value"));
            var a_name = this.model.get("attribute_name");
            if (a_name && a_name !== "") {
                this.$(".preview .label").html(this.model.get("feature_name") + " - " + a_name);
            } else {
                this.$(".preview .label").html(this.model.get("feature_name"));
            }
            this.$(".preview .value").html(this.model.get("final_value"));
            this.$(".preview").show();
            this.$(".edit").hide();
            this.$(".edit-spec-button").show();
        },
        "removeSpecButton" : function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (confirm("Really remove this?")) {

                $.ajax({
                    "url" : "/admin/ajax_motorcycle_spec_remove/<?php echo $id; ?>/" + this.model.get("motorcyclespec_id"),
                    "type" : "POST",
                    "dataType" : "json",
                    "data": {
                    },
                    "success" : _.bind(function(data) {
                        if (data.success) {
                            showGritter("Success", "Removed successfully");
                            $(this.el).remove(); // clear it out...
                        } else {
                            // error...
                            showGritter("Error", data.error_message);
                        }
                    }, this)
                });
            }
        },
        initialize: function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "removeSpecButton", "emptyAction", "showEditForm", "saveButton", "cancelButton");
        },
        "render" : function() {
            $(this.el).html(this.template(this.model.toJSON()));
            $(this.el).attr("data-motorcyclespec-id", this.model.get("motorcyclespec_id"));
            this.cancelButton();
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
            $.ajax({
                "url" : "/admin/ajax_motorcycle_specgroup_add/<?php echo $id; ?>",
                "type" : "POST",
                "dataType" : "json",
                "data": {
                },
                "success" : _.bind(function(data) {
                    if (data.success) {
                        // we have to add a new one...
                        var m = new SpecGroupModel(data.data.model);
                        this.options.specgroups.addOne(m);

                    } else {
                        // error...
                        showGritter("Error", data.error_message);
                    }
                }, this)
            });
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
            "active" => "specs",
            "descriptor" => "Specifications Options",
            "source" => @$product["source"],
            "stock_status" => @$product["stock_status"]
        ), true);

        ?>

        <!-- END TABS -->
        <?php echo form_open('admin/update_motorcycle/' . $id, array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">

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

    .ui-state-highlight {
        background-color: yellow;
        border: 2px dashed red;
        height: 3em;
    }
</style>
