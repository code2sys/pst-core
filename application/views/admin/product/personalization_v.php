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
<style>
    .widget {
        border: 1px solid gray;
    }

    .widget .widget-head {
        border-bottom: 1px solid gray;
        padding: 1em;
        font-weight: bold;
        font-size: 125%;
        font-style: italic;
        background-color: #dddddd;
    }

    .widget .widget-head h4.heading {
        margin: 0;
    }

    .widget .widget-body {
        background: white;
        padding: 1em;
    }

    div[role=dialog] {
        background-color: gray;
        border: 1px solid black;
    }

    div[role=dialog] .ui-dialog-content {
        background-color: white;
        border: 1px solid black;
    }


    .no-close .ui-dialog-titlebar-close {
        display: none;
    }

    .ui-autocomplete li.ui-menu-item {
        background: white;
        border: 1px solid black;
    }

</style>

<script type="application/javascript">
    function cleanUpTime(time) {
        if (time) {
            return moment(time.replace(/\-/g, "/")).format('ddd MM/DD h:mm a');
        } else {
            return "";
        }
    }

    function showLoading(message) {
        console.log(message);
    }

    function hideLoading() {

    }

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

    /**
     * Created by jonathan on 5/22/14.
     */

    window.KnownModelModel = Backbone.Model.extend({
        default: {
            "machinetype" : "",
            "model" : "",
            "make" : ""
        }
    });

    window.KnownModelCollection = Backbone.Collection.extend({
        model: KnownModelModel,
        comparator: function(x) {
            return (x.get("machinetype") + " " + x.get("make") + " " + x.get("model")).toLowerCase();
        }
    });

    window.PartModel = Backbone.Model.extend({
        idAttribute: "part_id",
        defaults: {
            "name" : "",
            "part_id" : 0,
            "description" : 0,
            "revisionset_id" : 0
        }
    });

    window.PartCollection = Backbone.Collection.extend({
        model: PartModel,
        idAttribute: "part_id"
    });

    window.RevisionsetModel = Backbone.Model.extend({
        idAttribute: "revisionset_id",
        defaults: {
            revisionset_id: 0,
            created: "",
            status: "",
            adminuser_id : 0,
            modified: "",
            comment: ""
        },
        comparator: function(x) {
            return (x.get('name') ? x.get('name').toLowerCase() : "");
        }
    });

    window.StatusModel = Backbone.Model.extend({
        defaults: {
            active: "category"
        }
    });

    window.PartQuestionModel = Backbone.Model.extend({
        idAttribute: "partquestion_id",
        defaults: {
            partquestion_id : 0,
            question: "",
            part_id: 0,
            created : ""
        }
    });

    window.PartQuestionCollection = Backbone.Collection.extend({
        model: PartQuestionModel,
        idAttribute: "partquestion_id"
    });

    window.PartQuestionAnswerModel = Backbone.Model.extend({
        idAttribute: "partquestionanswer_id",
        defaults: {
            partquestionanswer_id : 0,
            answer: "",
            partquestion_id: 0,
            created : ""
        }
    });

    window.PartQuestionAnswerCollection = Backbone.Collection.extend({
        model: PartQuestionAnswerModel,
        idAttribute: "partquestionanswer_id"
    });

    window.PartQuestionAnswerPartVariationModel = Backbone.Model.extend({
        idAttribute: "partquestionanswerpartvariation_id",
        defaults: {
            partquestionanswerpartvariation_id : 0,
            created : "",
            partquestionanswer_id : 0,
            partvariation_id : 0,
            answer : "",
            distributor_id : 0,
            distributor_name : "",
            part_number: "",
            quantity_available: 0,
            quantity_last_updated : "",
            quantity_ten_plus : 0,
            stock_code : ""
        }
    });

    window.PartQuestionAnswerPartVariationCollection = Backbone.Collection.extend({
        model: PartQuestionAnswerPartVariationModel,
        idAttribute: "partquestionanswerpartvariation_id"
    });

    window.PartVariationModel = Backbone.Model.extend({
        idAttribute: "partvariation_id",
        defaults: {
            created : "",
            partvariation_id : 0,
            distributor_id : 0,
            distributor_name : "",
            part_number: "",
            quantity_available: 0,
            quantity_last_updated : "",
            quantity_ten_plus : 0,
            stock_code : ""
        }
    });

    window.PartVariationCollection = Backbone.Collection.extend({
        model: PartQuestionAnswerPartVariationModel,
        idAttribute: "partquestionanswerpartvariation_id"
    });

    window.PartQuestionAnswerFitmentModel = Backbone.Model.extend({
        idAttribute: "partquestionanswerfitment_id",
        defaults: {
            created : "",
            partquestionanswer_id : 0,
            model_id : 0,
            make_id : 0,
            machinetype_id : 0,
            year : 0,
            answer : "",
            partquestion_id : 0
        }
    });

    window.PartQuestionAnswerFitmentCollection = Backbone.Collection.extend({
        model: PartQuestionAnswerFitmentModel,
        idAttribute: "partquestionanswerfitment_id"
    });

    window.DistributorModel = Backbone.Model.extend({
        idAttribute: "distributor_id",
        defaults: {
            "distributor_id" : 0,
            "created" : "",
            "name" : "",
            "active" : 1,
            "contact1" : "",
            "contact2" : "",
            "phone1" : "",
            "phone2" : "",
            "website" : "",
            "notes" : ""
        }
    });

    window.DistributorCollection = Backbone.Collection.extend({
        model : DistributorModel,
        idAttribute: "distributor_id"
    });

    window.PartNumberModel = Backbone.Model.extend({
        idAttribute: "partnumber_id",
        defaults: {
            "partnumber_id" : 0,
            "created" : "",
            "partnumber" : "",
            "universalfit" : 0
        }
    });

    window.PartNumberCollection = Backbone.Collection.extend({
        model : PartNumberModel,
        idAttribute: "partnumber_id"
    });

    window.PartPartNumberModel = Backbone.Model.extend({
        idAttribute: "partpartnumber_id",
        defaults: {
            "partpartnumber_id" : 0,
            "partnumber_id" : 0,
            "part_id" : 0,
            "created" : ""
        }
    });

    window.PartPartNumberCollection = Backbone.Collection.extend({
        model : PartPartNumberModel,
        idAttribute: "partpartnumber_id"
    });

    window.PartNumberPartQuestionmodel = Backbone.Model.extend({
        idAttribute: "partnumberpartquestion_id",
        defaults: {
            "partnumberpartquestion_id" : 0,
            "partnumber_id" : 0,
            "partquestion_id" : 0,
            "answer" : ""
        }
    });

    window.PartNumberPartQuestionCollection = Backbone.Collection.extend({
        model : PartNumberPartQuestionmodel,
        idAttribute: "partnumberpartquestion_id"
    });

    window.PartNumberModelModel = Backbone.Model.extend({
        idAttribute: "partnumbermodel_id",
        defaults: {
            "partnumbermodel_id" : 0,
            "partnumber_id" : 0,
            "model_id" : 0,
            "model_name" : "",
            "make_id" : 0,
            "make_name" : "",
            "year" : 0,
            "machinetype_id" : 0,
            "machinetype_name" : ""
        }
    });

    window.PartNumberModelCollection = Backbone.Collection.extend({
        model : PartNumberModelModel,
        idAttribute: "partnumbermodel_id",
        comparator: function(x) {
            return x.get('machinetype_name') + " - " + x.get("make_name") + " - " + x.get("model_name") + " - " + x.get("year");
        }
    });

</script>

<script type="text/template" id="PartPersonalizationQuestionAnswerDistributorPartView">
    <td><a href="#" class="btn btn-block btn-default btn-icon glyphicons delete"><i></i>DELETE</a></td>
    <td><%= obj.distributor_name %></td>
    <td><%= obj.part_number %></td>
</script>
<script type="text/template" id="PartPersonalizationQuestionAnswerView">
    <h4 class="heading-block">Answer: <%= obj.answer %></h4>
    <% if (!obj.readonly) { %>
    <form>
        <strong>Answer:</strong> <input type="text" name="answer" value="<%= obj.answer %>" />
        <input type="submit" class="updateanswerbutton" value="Update Answer" />
        <input type="submit" class="deleteanswerbutton" value="Delete Answer" />
    </form>
    <% } %>
    <br/>
    <table class="table table-primary table-bordered table-vertical-center modifiedth" width="100%">
        <thead>
        <tr>
            <th class="center" >Fitment</th>
            <th class="center" >Distributor</th>
            <th class="center" >Part #</th>
            <th class="center">MSRP</th>
            <th class="center">Qty Available</th>
            <th class="center">Cost</th>
            <th class="center">Closeout?</th>
            <th class="center">Shipping Weight</th>
            <th class="center" ></th>

        </tr>
        </thead>
        <tbody class="PartPersonalizationQuestionAnswerViewtbody">


        </tbody>
    </table>
    <p>&nbsp;</p>
</script>

<script type="text/template" id="PartPersonalizationCreateNewAnswerView">
    <p><strong>Add New Answer</strong></p>

    <form>
        <input type="text" name="answer" placeholder="Enter Answer..." />
        <select name="distributor_id">
            <option value="0">Select Distributor</option>
        </select>
        <input type="text" name="part_number" placeholder="Part Number..." />
        <input type="text" name="price" placeholder="MSRP..."/>
        <input type="text" name="qty_available" placeholder="Qty Available..." />
        <input type="text" name="cost" placeholder="Cost..." />
        <a href="#" class="fitmentpopup">Edit Fitment</a>
        <label style="inline-block"><input type="checkbox" value="Closeout" name="stock_code" /> Closeout</label>
        <input type="text" name="weight" placeholder="Weight..." />
        <input type="submit" class="addanswer" value="Add Answer" />
        <div class="fitments">

        </div>
    </form>
</script>
<script type="text/template" id="PartPersonalizationQuestionView">
    <div class="innerLR">
        <div class="widget">
            <div class="widget-head">
                <h4 class="heading">Personalization Question: <%= obj.question %></h4>
            </div>
            <div class="widget-body">
                <% if (!obj.readonly) { %>
                <form>
                    <em>Question:</em> <input type="text" name="question" value="<%= obj.question %>" />
                    <input type="submit" class="updatebutton" value="Update Question" />
                    <input type="submit" class="deletebutton" value="Delete Question" />
                </form><br/>
<!--                <form>-->
<!--                <label><input type="radio" name="productquestion" value="0" <% if (!(parseInt(obj.productquestion, 10) > 0)) { %>checked='checked'<% } %> /> Required Question</label>-->
<!--                <label><input type="radio" name="productquestion" value="1" <% if (parseInt(obj.productquestion, 10) > 0) { %>checked='checked'<% } %> /> Filter Question</label>-->
<!--                </form>-->
                <% } %>
                <div class="answerdiv">

                </div>

            </div>
        </div>
    </div>
    <div class="innerLR">
        &nbsp;<br/>
    </div>
</script>
<script type="text/template" id="PartPersonalizationNewQuestionView">
    <div class="innerLR">
        <div class="widget">
            <div class="widget-head">
                <h4 class="heading">Create New Question</h4>
            </div>
            <div class="widget-body">
                <form>
                    <input type="text" name="question" placeholder="Enter Question..." />
                    <input type="submit" class="addbutton" value="Create Question" />
                </form>

            </div>
        </div>
    </div>
</script>
<script type="text/template" id="PartPersonalizationView">
    <div class="fitment"></div>
    <div class='questions'></div>
</script>
<script type="text/template" id="ReadOnlyFitmentList">
    <td colspan=3>
        <ul>

        </ul>
        <p>
            <a href="#" class="hidebutton">Hide Fitment</a>
        </p>
    </td>
</script>
<script type="text/template" id="PartPersonalizationPartNumberPartVariationRow">
    <td width="33%"><%= obj.distributor_name %></td>
    <td width="33%"><%= obj.part_number %></td>
    <% if (!obj.readonly) { %>
    <td width="33%"><a href="#" class="removelink">Delete</a></td>
    <% } %>
</script>
<script type="text/template" id="PartPersonalizationPartNumberRow">
    <td class="fitmentcell"><em></em> <a href="#" class="fitment">Edit Fitment</a><a href="#" class="hidefitment" style="display: none">Hide Fitment</a></td>
    <td ><%= obj.distributor_name %></td>
    <td ><%= obj.part_number %></td>
    <td ><input type="text" name="price" value="<%= obj.price %>" /></td>
    <td ><input type="text" name="qty_available" value="<%= obj.qty_available %>" /></td>
    <td ><input type="text" name="cost" value="<%= obj.cost %>" /></td>
    <td align="center"><input type="checkbox" name="stock_code" value="Closeout" <% if (obj.stock_code == 'Closeout') { %>checked='checked'<% } %> /> </td>
    <td ><input type="text" name="weight" value="<%= obj.weight %>" /></td>
    <td ><a href="#" class="removelink">Delete</a></td>
 </script>
<script type="text/template" id="EditPopoverView">
<td colspan="8">
                        <div style="width: 45%; float: left">
                            <p><strong>Add a Fitment Rule</strong></p>

                            <p>
                                Add a fitment rule by typing in a machine type (e.g., ATV, Dirt Bike) and selecting it from the list of suggestions (or enter a new machine type if not matched in the list).  Then, you provide the make (e.g., Honda, Suzuki), again selecting from the suggestions if possible.  Then, enter the model and year.  You can enter multiple years with a dash, e.g., 1999-2014.
                            </p>

                            <form>
                                <div class="form-group">
                                    <label for="machinetype">Machine Type</label>
                                    <input name="machinetype" type="text" class="form-control selectMachine" id="machinetype" placeholder="Machine Type..." />
                                </div>
                                <div class="form-group">
                                    <label for="make">Make</label>
                                    <input name="make" type="text" class="form-control selectMake" id="make" placeholder="Make..." />
                                </div>
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input name="model" type="text" class="form-control selectModel" id="model" placeholder="Model..." />
                                </div>
                                <div class="form-group">
                                    <label for="EditPopoverViewyear">Year</label>
                                    <input name="year" type="text" class="form-control selectYear" id="EditPopoverViewyear" placeholder="YYYY or YYYY-YYYY" />
                                    <p class="help-block">Enter year as YYYY or YYYY-YYYY for a range of years.</p>
                                </div>

                                <button type="submit" class="btn btn-default addFitment">Add Fitment</button>
                            </form>


                        </div>
                        <div style="width: 45%; float: right">
                            <p><strong>Edit Fitment</strong></p>
                                <p class="nofitmentwarning"><em>No Fitment! This will be treated as universal fit.</em></p>

                                <table class="fitment table" width="100%">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Machine Type</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Year</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>


        <div style="clear: both"></div>
    </td>
</script>
<script type="text/template" id="EditPopoverFitmentRowView">
    <td><a href="#" class="remove glyphicon remove_2"><i></i> Delete</a></td>
    <td><%= obj.machinetype_name %></td>
    <td><%= obj.make_name %></td>
    <td><%= obj.model_name %></td>
    <td><%= obj.year %></td>
</script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cube"></i>&nbsp;Edit Product: <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

        <br>

        <!-- ERROR -->
        <?php if (@$error): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <p><br/><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <p><br/><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->

        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "personalization"
        ), true);
        ?>
        <!-- END TABS -->

        <style>
            td.row0, tr.row0 {
                background: #fff;
            }
            td.row1, tr.row1 {
                background: #ccc;
            }
        </style>
        <script type="application/javascript">
            // Sleight of hand: The revisionset is just this part. That's all it is...
            var revisionSet = new RevisionsetModel(<?php echo json_encode($revisionset); ?>);
            var statusModel = new StatusModel();
            var current_category_id = 0;
        </script>
        <script type="application/javascript">

            window.PartPersonalizationPartNumberPartVariationRow = Backbone.View.extend({
                className: "PartPersonalizationPartNumberPartVariationRow",
                template: _.template($("#PartPersonalizationPartNumberPartVariationRow").html()),
                events: {
                    "click .removelink" : "remove"
                },
                initialize: function(options) {
                    this.options = options || {};
                    this.model.set("readonly", this.options.readonly);
                    _.bindAll(this, "render", "remove");
                },
                "remove" : function(e) {
                    e.preventDefault();

                    if (confirm("Really remove this distributor part?")) {
                        showLoading("Removing distributor part...");



                        $.ajax({
                            "url" : "/adminproduct/deletePartVariation/<?php echo $part_id; ?>/" + this.options.partquestion_id + "/" + this.model.get('partvariation_id'),
                            "type" : "POST",
                            "dataType" : "json",
                            "data": {
                            },
                            "success" : _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", data.success_message);
                                    setTimeout(_.bind(function() {
                                        var partnumber_id = this.model.get("partnumber_id");
                                        var partvariation_id = this.model.get("partvariation_id");
                                        console.log(["Part number", partnumber_id]);

                                        console.log(["Removing model", this.model]);
                                        app.PartVariationCollection.remove(this.model);
                                        console.log("Removing this row.");
                                        $(this.el).remove();

                                        // clean it up...you may be deleting things...
                                        var matches = _.filter(app.PartVariationCollection.models, function (x) {
                                            return (x.get('partnumber_id') == partnumber_id) && (x.get("partvariation_id") != partvariation_id);
                                        }, this);

                                        if (matches.length == 0) {
                                            console.log("No matching part variationss.");
                                            // remove that part number.
                                            var pn = _.filter(app.PartNumberPartQuestionCollection.models, function (x) {
                                                return x.get('partnumber_id') == partnumber_id;
                                            });
                                            console.log(["Matching part number part question", pn]);
                                            _.each(pn, function (p) {
                                                console.log(["Removing pat number.", p]);
                                                app.PartNumberPartQuestionCollection.remove(p);
                                            });
                                        } else {
                                            console.log(["Matches", matches]);
                                        }
                                        app.PartVariationCollection.trigger('reset');
                                    }, this));

                                } else {
                                    // error...
                                    showGritter("Error", data.error_message);
                                }
                            }, this),
                            "complete" : function() {
                                hideLoading();
                            }
                        });
                    }
                },
                "render" : function() {
                    $(this.el).html(this.template(this.model.toJSON()));
                    return this;
                },
                tagName: "tr"
            });


            window.PartPersonalizationPartNumberRow = Backbone.View.extend({
                className: "PartPersonalizationPartNumberRow",
                template: _.template($("#PartPersonalizationPartNumberRow").html()),
                events: {
                    "click .removelink" : "remove",
                    "click .fitment" : "fitment",
                    "click .hidefitment" : "hidefitment",
                    "change input[name=stock_code]" : "stock_code",
                    "change input[name=price]" : "update_local_settings",
                    "change input[name=qty_available]" : "update_local_settings",
                    "change input[name=cost]" : "update_local_settings",
                    "change input[name=weight]" : "update_local_settings"
                },
                update_local_settings : function(e) {
                    var price = this.$("input[name=price]").val();
                    var qty_available = this.$("input[name=qty_available]").val();
                    var cost = this.$("input[name=cost]").val();
                    var weight = this.$("input[name=weight]").val();

                    // Now, phone home
                    $.ajax({
                        "url" : "/adminproduct/update_local_settings/" + "<?php echo $part_id; ?>",
                        "type" : "POST",
                        "dataType" : "json",
                        "async" : false,
                        "data" : {
                            "partnumber_id" : this.model.get('partnumber_id'),
                            price: price,
                            qty_available: qty_available,
                            cost: cost,
                            weight: weight
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                this.block_redraw_action = true;
                                this.model.set('price', price);
                                this.model.set('qty_available', qty_available);
                                this.model.set('cost', cost);
                                this.pv.set('price', price);
                                this.pv.set('qty_available', qty_available);
                                this.pv.set('cost', cost);
                                this.pv.trigger("change");
                            } else {
                                showGritter("error", data.error_message);
                            }

                        }, this)
                    });
                },
                stock_code : function(e) {
                    var status = "Normal";
                    if (this.$("input[name=stock_code]:checked").length > 0) {
                        // phone home and say it is closeout
                        status = "Closeout";
                    }

                    // Now, phone home
                    $.ajax({
                        "url" : "/adminproduct/mark_product_status/" + "<?php echo $part_id; ?>",
                        "type" : "POST",
                        "dataType" : "json",
                        "async" : false,
                        "data" : {
                            "partnumber_id" : this.model.get('partnumber_id'),
                            "stock_code" : status
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                this.block_redraw_action = true;
                                this.model.set('status', status);
                           } else {
                                showGritter("error", data.error_message);
                            }

                        }, this)
                    });
                },
                initialize: function(options) {
                    //console.log("Here at initialize");
                    this.options = options || {};
                    _.bindAll(this, "stock_code", "render", "remove", "fitment", "addfitment", "removefitment", "recalculatefitment", "hidefitment");

                    this.pv = _.find(app.PartVariationCollection.models, function(x) {
                        return (x.get('partnumber_id') == this.model.get('partnumber_id'));
                    }, this);
                    if (this.pv) {
                        var pv = this.pv;
                        this.model.set("cost", pv.get("cost"));
                        this.model.set("price", pv.get("price"));
                        this.model.set("qty_available", pv.get("qty_available"));
                        this.model.set("distributor_name", pv.get("distributor_name"));
                        this.model.set("part_number", pv.get("part_number"));
                        this.model.set("stock_code", pv.get("stock_code"));
                        this.pv.bind("change", this.render);
                    }

                    this.model.set("readonly", this.options.readonly);
                    this.model.bind("change", this.render);
                    app.PartVariationCollection.bind("add", this.render);
                    app.PartVariationCollection.bind("reset", this.render);
                    this.block_redraw_action = false;
                },
                "recalculatefitment" : function() {
                    this.fitments = new PartNumberModelCollection(_.filter(app.PartNumberModelCollection.models, function(x) {
                        return x.get('partnumber_id') == this.model.get('partnumber_id');
                    }, this));

                    if (this.fitments.length > 0) {
                        // there should be fitments...
                        this.model.set("universalfit", 0);
                        var fitment_string = "";
                        fitment_string = _.map(this.fitments.models, function(x) { return x.get('make_name') + " " + x.get('model_name') + ' ' + x.get('year'); }).join("; ");

                        if (fitment_string.length > 100) {
                            fitment_string = fitment_string.substring(0,100) + "...";
                        }

                        this.$(".fitmentcell em").html(fitment_string);
                    } else {
                        this.model.set("universalfit", 1);
                        this.$(".fitmentcell em").html('Universal Fit');
                    }
                },
                "remove" : function(e) {
                    e.preventDefault();
                    console.log(["Remove fn", this.model]);
                    $.ajax({
                        "url" : "/adminproduct/removeAnswerPart/" + "<?php echo $part_id; ?>" + "/" + this.model.get("partquestion_id") + "/" + this.model.get("partnumber_id"),
                        "type" : "POST",
                        "dataType" : "json",
                        "async" : false,
                        "data" : {
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                // Send down
                                try {
                                    app.PartNumberPartQuestionCollection.reset(data.PartNumberPartQuestionCollection);
                                } catch(err) {
                                    console.log("Error: " + err);
                                }
                                $(this.el).remove(); // remove this row....
                            } else {
                                showGritter("error", data.error_message);
                            }

                        }, this)
                    });
                },
                "hidefitment" : function(e) {
                    e.preventDefault();
                    this.$(".fitment").show();
                    this.$(".hidefitment").hide();
                    $(this.el).next().remove();

                },
                "fitment" : function(e) {
                    e.preventDefault();
                    this.$(".fitment").hide();
                    this.$(".hidefitment").show();
                    // we must do something.
                    console.log(["Model to extract title:", this.model]);
                    $(this.el).after(new EditPopoverView({
                        "title" : "Answer " + this.options.title,
                        "collection" : this.fitments,
                        "add_fitment_callback" : _.bind(function(param) {
                            this.addfitment(this.model.get('partnumber_id'), param.machinetype, param.make, param.model, param.year, this.recalculatefitment);
                            console.log(["Add fitment callback", param]);
                            return new PartNumberModelModel({
                                "partnumbermodel_id" : "new" + param.machinetype + "_" + param.make + "_" + param.model + "_" + param.year,
                                "partnumber_id" : this.model.get('partnumber_id'),
                                "machinetype_name" : param.machinetype,
                                "make_name" : param.make,
                                "model_name": param.model,
                                "year" : param.year
                            });
                        }, this),
                        "remove_fitment_callback" : _.bind(function(m) {
                            console.log(["Remove fitment callback", m]);
                            this.fitments.remove(m);
                            app.PartNumberModelCollection.remove(m);
                            var mn = _.find(app.PartNumberModelCollection.models, function(x) {
                                return (x.get('partnumber_id') == this.model.get('partnumber_id')) && (x.get('machinetype_name') && (x.get('machinetype_name').toLowerCase() == m.get('machinetype_name').toLowerCase()))  && (x.get('make_name') && (x.get('make_name').toLowerCase() == m.get('make_name').toLowerCase()))  && (x.get('model_name') && (x.get('model_name').toLowerCase() == m.get('model_name').toLowerCase()))  && (x.get('year') && (x.get('year') == m.get('year')));
                            }, this);
                            if (mn) {
                                app.PartNumberModelCollection.remove(mn);
                            }
                            this.recalculatefitment();
                            this.removefitment(this.model.get('partnumber_id'), m.get('machinetype_name'), m.get('make_name'), m.get('model_name'), m.get('year'), this.recalculatefitment);
                        }, this)
                    }).render().el);
                },
                "addfitment" : function(partnumber_id, machinetype, make, model, year, callback) {
                    $.ajax({
                        "url" : "/adminproduct/addfitment/" + "<?php echo $part_id; ?>",
                        "type" : "POST",
                        "dataType" : "json",
                        "async" : false,
                        "data" : {
                            "partnumber_id" : partnumber_id,
                            "machinetype" : machinetype,
                            "make" : make,
                            "model" : model,
                            "year" : year
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                // let's turn this off.
                                this.model.set('universalfit', 0);

                                // there could be many of these...
                                app.PartNumberModelCollection.reset(data.data.data.PartNumberModelCollection);
                                app.KnownModelCollection.reset(data.data.data.KnownModelCollection);


                                if (callback) {
                                    // we send back a new model...
                                    callback();
                                }
                            } else {
                                showGritter("error", data.error_message);
                            }

                        }, this)
                    });
                },
                "removefitment" : function(partnumber_id , machinetype, make, model, year, callback ) {
                    //// tell the server about it
                    $.ajax({
                        "url" : "/adminproduct/removefitment/" + "<?php echo $part_id; ?>",
                        "type" : "POST",
                        "dataType" : "json",
                        "async" : false,
                        "data": {
                            "partnumber_id" : partnumber_id,
                            "machinetype" : machinetype,
                            "make" : make,
                            "model" : model,
                            "year" : year
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                // there could be many of these...
                                var c = new PartNumberModelCollection(data.data.data.PartNumberModelCollection);
                                _.each(c.models, function(x) {
                                    app.PartNumberModelCollection.add(x);
                                });

                                if (callback) {
                                    callback();
                                }
                            } else {
                                // error...
                                showGritter("Error", data.error_message);
                            }
                        }, this)
                    });

                },
                "render" : function() {
                    if (this.block_redraw_action) {
                        this.block_redraw_action = false;
                        return;
                    }

                    console.log("PartPersonalizationPartNumberRow");
                    console.log(this.model);
                    $(this.el).html(this.template(this.model.toJSON()));
                    $(this.el).addClass("row" + this.options.stylecounter);
                    this.$("td").addClass("row" + this.options.styleCounter);

                    this.recalculatefitment();

                    return this;
                },
                tagName: "tr"
            });


            window.PartPersonalizationQuestionAnswerDistributorPartView = Backbone.View.extend({
                template: _.template($("#PartPersonalizationQuestionAnswerDistributorPartView").html()),
                className: "PartPersonalizationQuestionAnswerDistributorPartView",
                initialize: function(options) {
                    this.options = options || {};
                    this.model.set('readonly', this.options.readonly);
                    _.bindAll(this, "render", "remove");
                },
                remove : function(e) {
                    e.preventDefault();
                    if (confirm('Really remove distributor part?')) {
                        showLoading("Removing distributor part...");

                        $.ajax({
                            "url" : "/adminproduct/deleteAnswerVariation/" + "<?php echo $part_id; ?>" + "/" + "<?php echo $part_id; ?>" + "/" + this.options.partquestion_id + "/" + this.model.get('partquestionanswer_id') + "/" + this.model.get("partvariation_id"),
                            "type" : "POST",
                            "dataType" : "json",
                            "data": {
                            },
                            "success" : _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", data.success_message);

                                    // update the collection
                                    app.PartQuestionAnswerCollection.reset(data.data.data.PartQuestionAnswerCollection); // this should be a complete collection.
                                    app.PartVariationCollection.reset(data.data.data.PartVariationCollection); // this should be a complete collection.
                                    app.PartQuestionAnswerPartVariationCollection.reset(data.data.data.PartQuestionAnswerPartVariationCollection); // this should be a complete collection.
                                } else {
                                    // error...
                                    showGritter("Error", data.error_message);
                                }
                            }, this),
                            "complete" : function() {
                                hideLoading();
                            }
                        });
                    }
                },
                events : {
                    "click .btn" : "remove"
                },
                render: function() {
                    // stub!
                    $(this.el).html(this.template(this.model.toJSON()));
                    return this;
                },
                tagName: "tr"
            });


            window.PartPersonalizationQuestionAnswerView = Backbone.View.extend({
                className: "PartPersonalizationQuestionAnswerView",
                template: _.template($("#PartPersonalizationQuestionAnswerView").html()),
                events: {
                    "click .updateanswerbutton" : "update",
                    "click .deleteanswerbutton" : "remove",
                    "submit form" : "nullaction"
                },
                update : function(e) {
                    e.preventDefault();
                    showLoading("Updating answer...");
                    var answer = this.$("input[name=answer]").val().trim();

                    $.ajax({
                        "url" : "/adminproduct/updateAnswer/" + "<?php echo $part_id; ?>" + "/" + "<?php echo $part_id; ?>" + "/" + this.options.partquestion_id + "/" + this.model.get('partquestionanswer_id'),
                        "type" : "POST",
                        "dataType" : "json",
                        "data": {
                            "answer" : answer
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                showGritter("Success", data.success_message);

                                // just update everything here as it touches the answer...
                                var old_answer = this.model.get('answer');
                                this.model.set('answer', answer);

                                _.each(_.filter(app.PartNumberPartQuestionCollection.models, function(x) {
                                    return (x.get('partquestion_id') == this.model.get('partquestion_id')) && (x.get('answer') == old_answer);
                                }, this), function(x) {
                                    x.set('answer', answer);
                                });

                                var f = _.find(app.PartQuestionAnswerCollection.models, function(x) {
                                    return x.get('partquestionanswer_id') == this.model.get('partquestionanswer_id');
                                }, this);

                                if (f) {
                                    f.set("answer", answer);
                                } else {
                                    console.log("Could not find the ID " + this.model.get("partquestionanswer_id") + " in PartQuestionAnswerCollection.");
                                }

                                f = _.filter(app.PartVariationCollection.models, function(x) {
                                    return x.get('partquestionanswer_id') == this.model.get('partquestionanswer_id');
                                }, this);

                                if (f && f.length > 0) {
                                    _.each(f, function(x) {
                                        x.set("answer", answer);
                                    }, this);
                                }

                            } else {
                                // error...
                                showGritter("Error", data.error_message);
                            }
                        }, this),
                        "complete" : function() {
                            hideLoading();
                        }
                    });
                },
                remove : function(e) {
                    e.preventDefault();
                    if (confirm("Really remove this answer?")) {
                        showLoading("Removing answer...");

                        $.ajax({
                            "url" : "/adminproduct/deleteAnswer/" + "<?php echo $part_id; ?>" + "/" + "<?php echo $part_id; ?>" + "/" + this.options.partquestion_id + "/" + this.model.get('partquestionanswer_id'),
                            "type" : "POST",
                            "dataType" : "json",
                            "data": {
                            },
                            "success" : _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", data.success_message);

                                    // delete these...
                                    var f = _.filter(app.PartQuestionAnswerCollection.models, function(x) {
                                        return x.get("partquestionanswer_id") == this.model.get("partquestionanswer_id");
                                    }, this);

                                    if (f && f.length > 0) {
                                        _.each(f, function(x) {
                                            app.PartQuestionAnswerCollection.remove(x);
                                        }, this);
                                    }

                                    $(this.el).remove();
                                    app.PartQuestionAnswerCollection.trigger('reset');
                                } else {
                                    // error...
                                    showGritter("Error", data.error_message);
                                }
                            }, this),
                            "complete" : function() {
                                hideLoading();
                            }
                        });
                    }

                },
                nullaction : function(e) {
                    e.preventDefault();

                },
                initialize: function(options) {
                    this.options = options || {};
                    this.model.set('readonly', this.options.readonly);
                    _.bindAll(this, "update", "remove", "nullaction");
                    app.PartNumberPartQuestionCollection.bind("reset", this.render);
                },
                render: function() {
                    if (this.model) {
                        //console.log("PartPersonalizationQuestionAnswerView");
                        //console.log(this.model);
                        $(this.el).html(this.template(this.model.toJSON()));

                        setTimeout(_.bind(function() {
                            var k = 0;
                            var filter = _.filter(app.PartNumberPartQuestionCollection.models, function(x) {
                                return (x.get('partquestion_id') == this.model.get('partquestion_id')) && (x.get('answer') == this.model.get('answer'));
                            }, this);
                            for (var i = 0; i < filter.length; i++) {
                                //console.log(i);
                                var x = filter[i];
                                // now add it.
                                this.$('tbody.PartPersonalizationQuestionAnswerViewtbody').append((new PartPersonalizationPartNumberRow({
                                    model: x, revisionset_id : "<?php echo $part_id; ?>", part_id : "<?php echo $part_id; ?>", partquestion_id : this.options.partquestion_id, stylecounter: k, readonly: this.options.readonly, title: this.model.get("answer")
                                })).render().el);
                                k = 1 - k;
                            }
                        }, this));

                    } else {
                        $(this.el).html("");
                    }

                    return this;
                }

            });

            window.EditPopoverFitmentRowView = Backbone.View.extend({
                className: "EditPopoverFitmentRowView",
                template: _.template($("#EditPopoverFitmentRowView").html()),
                initialize: function(options) {
                    this.options = options || {};
                    _.bindAll(this, "render", "remove");
                },
                "events" : {
                    "click .remove" : "remove"
                },
                "render" : function() {
                    $(this.el).html(this.template(this.model.toJSON()));
                    return this;
                },
                "remove" : function(e) {
                    e.preventDefault();
                    // call upstream.
                    this.options.remove_callback(this.model);
                    // and then, remove this
                    $(this.el).remove();
                },
                "tagName" : "tr"
            });

            // remove_fitment_callback
            // add_fitment_callback
            window.EditPopoverView = Backbone.View.extend({
                className: "EditPopoverView",
                template: _.template($("#EditPopoverView").html()),
                initialize: function(options) {
                    this.options = options || {};
                    _.bindAll(this, "render", "addFitment", "removeFitment", "selectMachine", "selectMake", "addRow", "resetTable", "buttonEditPopoverVieweditform", "buttonEditPopoverViewaddform");
                    //this.collection.bind("add", this.addRow);
                    //this.collection.bind("reset", this.resetTable);
                    $(window).on("click", "#buttonEditPopoverViewaddform", this.buttonEditPopoverViewaddform);
                    $(window).on("click", "#buttonEditPopoverVieweditform", this.buttonEditPopoverVieweditform);
                },
                "buttonEditPopoverViewaddform" : function(e) {
                    console.log("Call to buttonEditPopoverViewaddform");
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    $("#EditPopoverViewaddform").show();
                    $("#EditPopoverVieweditform").hide();
                    $("a.buttonEditPopoverViewaddform").addClass("active");
                    $("a.buttonEditPopoverVieweditform").removeClass("active");
                },
                "buttonEditPopoverVieweditform" : function(e) {
                    console.log("Call to buttonEditPopoverVieweditform");
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    $("#EditPopoverViewaddform").hide();
                    $("#EditPopoverVieweditform").show();
                    $("a.buttonEditPopoverViewaddform").removeClass("active");
                    $("a.buttonEditPopoverVieweditform").addClass("active");
                },
                "addRow" : function(x) {
                    this.$(".nofitmentwarning").hide();
                    this.$("table.fitment").show();
                    this.$("table.fitment tbody").append((new EditPopoverFitmentRowView({model: x, "remove_callback" : this.removeFitment})).render().el);
                },
                "resetTable" : function(x) {
                    this.$(".nofitmentwarning").show();
                    this.$("table.fitment").hide();
                    this.$("table.fitment tbody").html("");
                },
                "events" : {
                    "click .addFitment" : "addFitment",
                    "change .selectMachine" : "selectMachine",
                    "change .selectMake" : "selectMake",
                    "autocompletechange .selectMachine" : "selectMachine",
                    "autocompletechange .selectMake" : "selectMake"
//                    "click .buttonEditPopoverViewaddform" : "buttonEditPopoverViewaddform",
//                    "click .buttonEditPopoverVieweditform" : "buttonEditPopoverVieweditform"
                },
                render: function() {
                    $(this.el).html(this.template({
                        title: this.options.title
                    }));

                    // populate the selects...
                    this.$("input[name=year]").prop('disabled', true);
                    this.$("input[name=model]").autocomplete({
                        source: []
                    });
                    this.$("input[name=model]").prop('disabled', true);
                    this.$("input[name=make]").autocomplete({
                        source: []
                    });
                    this.$("input[name=make]").prop('disabled', true);

                    // and, finally, do the machine types.
                    this.$("input[name=machinetype]").autocomplete({
                        source: _.map(_.countBy(app.KnownModelCollection.models, function(x) {
                            return x.get("machinetype");
                        }), function(value, key) {
                            return key;
                        }).sort()
                    });

                    // add the fitments...
                    if (this.collection.length > 0) {

                        _.each(this.collection.models, function(x) {
                            this.addRow(x);
                        }, this);

                        setTimeout(this.buttonEditPopoverVieweditform);
                    }

                    return this;
                },
                "addFitment": function(e) {
                    e.preventDefault();
                    var valid = true;
                    var machinetype = this.$("input[name=machinetype]").val();
                    var make = this.$("input[name=make]").val();
                    var models = [];
                    var model_string = this.$("input[name=model]").val().trim();
                    models = model_string.split("/");



                    var years = [];
                    var year_string = this.$("input[name=year]").val().trim();
                    if (year_string.match(/^[0-9]{4}\-[0-9]{4}$/)) {
                        var bits = year_string.split("-");
                        for (var q = parseInt(bits[0], 10); q <= parseInt(bits[1], 10); q++) {
                            years.push(q);
                        }
                    } else if (year_string.match(/^[0-9]{4}/)) {
                        years = [ parseInt(year_string, 10) ];
                    } else {
                        valid = false;
                    }


                    if ((models.length == 0) || (years.length == 0)) {
                        alert("Please provide at least one model and year.");
                        valid = false;
                    }

                    if (valid) {
                        var i; var j;
                        for (i = 0;i < models.length; i++) {
                            for (j = 0; j < years.length; j++) {
                                // check that this does not already exist...
                                if (!_.find(this.collection.models, function(x) {
                                        return (x.get("machinetype_name") && (x.get("machinetype_name").toLowerCase() == machinetype.toLowerCase()) ) &&  (x.get("model_name") && (x.get("model_name").toLowerCase() == models[i].toLowerCase())) &&  (x.get("make_name") && (x.get("make_name").toLowerCase() == make.toLowerCase())) && (x.get('year') == years[j]);
                                    }, this)) {
                                    this.collection.add(this.options.add_fitment_callback({
                                        'machinetype' : machinetype,
                                        "make" : make,
                                        "model" : models[i],
                                        "year" : years[j]
                                    }));
                                    console.log(["Adding", machinetype, make, models[i], years[j], this.collection]);

                                }
                            }
                        }

                        console.log(["Fitment collection", this.collection]);

                        this.$("table.fitment tbody").html('');
                        if (this.collection.length > 0) {
                            // hide the warning
                            this.$(".nofitmentwarning").hide();

                            // add the rows...
                            _.each(this.collection.models, function(x) {
                                this.addRow(x);
                            }, this);

                        } else {
                            // show the warning
                            this.$(".nofitmentwarning").show();
                        }

                        // switch tabs...
                        this.$("input[name=machinetype]").val("");
                        this.$("input[name=make]").val("");
                        this.$("input[name=model]").val("");
                        this.$("input[name=year]").val("");
                        this.buttonEditPopoverVieweditform();
                    }
                },
                "removeFitment" : function(model) {
                    this.collection.remove(model);
                    this.options.remove_fitment_callback(model);
                    if (this.collection.length == 0) {
                        this.$(".nofitmentwarning").show();
                        this.buttonEditPopoverViewaddform();
                    }
                },
                "selectMachine" : function(e) {
                    console.log("In selectMachine");
                    // clear the years
                    this.$("input[name=year]").val("");
                    this.$("input[name=year]").prop('disabled', true);

                    // clear the models
                    this.$("input[name=model]").val("");
                    this.$("input[name=model]").prop('disabled', true);

                    // populate the makes
                    var machinetype = this.$("input[name=machinetype]").val().trim().toLowerCase();
                    console.log("Machine type: " + machinetype);

                    if (machinetype != "") {
                        // do the map...
                        this.$("input[name=make]").prop('disabled', false);
                        // let's put something on there.
                        this.$("input[name=make]").autocomplete({
                            source: _.map(_.countBy(_.filter(app.KnownModelCollection.models, function(x) {
                                if (x.get('machinetype')) {
                                    return x.get('machinetype').toLowerCase() == machinetype;
                                } else {
                                    return false;
                                }
                            }), function(x) {
                                return x.get("make");
                            }), function(value, key) {
                                return key;
                            }).sort()
                        });
                        console.log("A");
                    } else {
                        this.$("input[name=make]").val("");
                        this.$("input[name=make]").prop('disabled', true);
                        console.log("B");
                    }

                },
                "selectMake" : function(e) {
                    // If there is indeed a make in there, it should replace the models with what is known about them...
                    var make = this.$("input[name=make]").val().trim().toLowerCase();

                    if (make != "") {
                        // do the map...on model this time
                        this.$("input[name=model]").autocomplete("destroy");
                        // let's put something on there.
                        this.$("input[name=model]").autocomplete({
                            source: _.map(_.countBy(_.filter(app.KnownModelCollection.models, function(x) {
                                if (x.get('make')) {
                                    return x.get('make').toLowerCase() == make;
                                } else {
                                    return false;
                                }
                            }), function(x) {
                                return x.get("model");
                            }), function(value, key) {
                                return key;
                            }).sort()
                        });
                        this.$("input[name=model]").prop('disabled', false);
                        this.$("input[name=year]").prop('disabled', false);
                    } else {
                        this.$("input[name=year]").val("");
                        this.$("input[name=year]").prop('disabled', true);

                        // clear the models
                        this.$("input[name=model]").val("");
                        this.$("input[name=model]").prop('disabled', true);
                    }
                },
                tagName: "tr"
            });


            window.PartPersonalizationCreateNewAnswerView = Backbone.View.extend({
                className: "PartPersonalizationCreateNewAnswerView",
                template: _.template($("#PartPersonalizationCreateNewAnswerView").html()),
                events: {
                    "click .addanswer": "save",
                    "submit form": "save",
                    "click .fitmentpopup" : "editfitment"
                },
                initialize: function(options) {
                    this.options = options || {};
                    _.bindAll(this, "render", "save", "editfitment");
                    this.fitments = new PartNumberModelCollection();
                },
                "editfitment" : function(e) {
                    e.preventDefault();
                    // TODO needs parameters
                    $(this.el).after(new EditPopoverView({
                        "title" : "Fitment for New Answer",
                        "collection" : this.fitments,
                        "add_fitment_callback" : _.bind(function(param) {
                            var m = new PartNumberModelModel({
                                "partnumbermodel_id" : "new" + param.machinetype + "_" + param.make + "_" + param.model + "_" + param.year,
                                "machinetype_name" : param.machinetype,
                                "make_name" : param.make,
                                "model_name": param.model,
                                "year" : param.year
                            });
                            this.fitments.add(m);
                            return m;
                        }, this),
                        "remove_fitment_callback" : _.bind(function(m) {
                            this.fitments.remove(m);
                        }, this)
                    }).render().el);
                },
                render: function() {
                    // stub!
                    $(this.el).html(this.template());

                    // better add our distributor options...
                    var mySelect = this.$('select[name=distributor_id]');
                    _.each(app.DistributorCollection.models, function(x) {
                        mySelect.append(
                            $('<option></option>').val(x.get('distributor_id')).html(x.get('name'))
                        );
                    });

                    return this;
                },
                save: function(e) {
                    e.preventDefault();
                    var answer = this.$("input[name=answer]").val();
                    var part_number = this.$("input[name=part_number]").val();
                    var distributor_id = this.$("select[name=distributor_id]").val();

                    showLoading("Adding question...");
                    $.ajax({
                        "url" : "/adminproduct/addAnswer/" + "<?php echo $part_id; ?>" + "/" + "<?php echo $part_id; ?>" + "/" + this.options.partquestion_id,
                        "type" : "POST",
                        "dataType" : "json",
                        "data": {
                            "answer" : answer,
                            "distributor_id" : distributor_id,
                            "part_number" : part_number,
                            "fitments" : _.map(this.fitments.models, function(x) {
                                return {
                                    "machinetype_name" : x.get("machinetype_name"),
                                    "make_name" : x.get("make_name"),
                                    "model_name" : x.get("model_name"),
                                    "year" : x.get("year")
                                };
                            }, this),
                            "cost" : this.$("input[name=cost]").val(),
                            "qty_available" : this.$("input[name=qty_available]").val(),
                            "price" : this.$("input[name=price]").val()
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                showGritter("Success", data.success_message);

                                // update the collection
                                app.PartNumberModelCollection.reset(data.data.data.PartNumberModelCollection);
                                app.KnownModelCollection.reset(data.data.data.KnownModelCollection);
                                app.PartQuestionAnswerCollection.reset(data.data.data.PartQuestionAnswerCollection); // this should be a complete collection.
                                app.PartVariationCollection.reset(data.data.data.PartVariationCollection); // this should be a complete collection.
                                app.PartQuestionAnswerPartVariationCollection.reset(data.data.data.PartQuestionAnswerPartVariationCollection); // this should be a complete collection.
                                app.PartNumberPartQuestionCollection.reset(data.data.data.PartNumberPartQuestionCollection);
                            } else {
                                // error...
                                showGritter("Error", data.error_message);
                            }
                        }, this),
                        "complete" : function() {
                            hideLoading();
                        }
                    });
                }
            });


            // this includes everything for a single question - that is, the question answers and the
            // so no events, it just has to get the
            window.PartPersonalizationQuestionView = Backbone.View.extend({
                className: "PartPersonalizationQuestionView",
                template: _.template($("#PartPersonalizationQuestionView").html()),
                // http://stackoverflow.com/questions/19325323/backbone-1-1-0-views-reading-options
                initialize: function(options) {
                    this.options = options || {};
                    _.bindAll(this, "render", "updateQuestion", "deleteQuestion", "nullaction", "subPatchCollection", "changeproductquestion");
                    this.model.set("readonly", this.options.readonly);
                    this.model.bind("change", this.render);
                },
                "changeproductquestion" : function(e) {
                    showLoading("Updating question...");
                    $.ajax({
                        "url" : "/adminproduct/changeProductQuestion/" + this.model.get("part_id") + "/" + this.model.get('partquestion_id'),
                        "type" : "POST",
                        "dataType" : "json",
                        "data": {
                            "productquestion" : this.$("input[name=productquestion]:checked").val()
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                showGritter("Success", data.success_message);
                                this.subPatchCollection(data.data.data);
                            } else {
                                // error...
                                showGritter("Error", data.error_message);
                            }
                        }, this),
                        "complete" : function() {
                            hideLoading();
                        }
                    });
                },
                "updateQuestion" : function(e) {
                    e.preventDefault();
                    showLoading("Updating question...");
                    var question = this.$("input[name='question']").val();
                    $.ajax({
                        "url" : "/adminproduct/updateQuestion/" + this.options.part.get('revisionset_id') + "/" + this.model.get("part_id") + "/" + this.model.get('partquestion_id'),
                        "type" : "POST",
                        "dataType" : "json",
                        "data": {
                            "question" : question
                        },
                        "success" : _.bind(function(data) {
                            if (data.success) {
                                showGritter("Success", data.success_message);
                                this.subPatchCollection(data.data.data);
                            } else {
                                // error...
                                showGritter("Error", data.error_message);
                            }
                        }, this),
                        "complete" : function() {
                            hideLoading();
                        }
                    });
                },
                "subPatchCollection" : function(data) {
                    console.log(["Data Received", data]);
                    var part_id = parseInt(this.model.get('part_id'), 10);
                    console.log(['Part ID; ', part_id]);
                    var thisPartQuestions = _.filter(app.PartQuestionCollection.models, function(y) {
                        return parseInt(y.get('part_id'), 10) == part_id;
                    }, this);

                    console.log(["thisPartQuestions", thisPartQuestions]);

                    var deletes = false;
                    var questionsSeen = {};
                    var questionsMap = {};

                    // Now, here's how this works: If the question is not mentioned, it has to be removed.  If the
                    _.each(thisPartQuestions, function(x) {
                        questionsMap[x.get('partquestion_id')] = x;
                    });

                    console.log(["Questions map", questionsMap]);

                    // update the collection
                    _.each(data, function(x) {
                        var partquestion_id = x.partquestion_id;
                        var obj = questionsMap[partquestion_id];
                        if (obj) {
                            for (var prop in x) {
                                obj.set(prop, x[prop]);
                                questionsSeen[partquestion_id] = true;
                            }
                        } else {
                            app.PartQuestionCollection.add(x);
                        }
                    });

                    console.log(["Questions seen", questionsSeen]);

                    _.each(thisPartQuestions, function(x) {
                        if (!questionsSeen[x.get('partquestion_id')]) {
                            console.log(["Not seen - removing", x]);
                            // delete this one.
                            deletes = true;
                            app.PartQuestionCollection.remove(x);
                        }
                    });

                    if (deletes) {
                        app.PartQuestionCollection.trigger("reset");
                    }
                },
                "deleteQuestion" : function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this question?')) {
                        showLoading("Removing question...");
                        $.ajax({
                            "url" : "/adminproduct/deleteQuestion/" + this.options.part.get('revisionset_id') + "/" + this.model.get("part_id") + "/" + this.model.get('partquestion_id'),
                            "type" : "POST",
                            "dataType" : "json",
                            "data": {
                            },
                            "success" : _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", data.success_message);
                                    this.subPatchCollection(data.data.data);
                                } else {
                                    // error...
                                    showGritter("Error", data.error_message);
                                }
                            }, this),
                            "complete" : function() {
                                hideLoading();
                            }
                        });
                    }
                },
                "nullaction" : function(e) {
                    e.preventDefault();
                },
                events: {
                    "click .updatebutton" : "updateQuestion",
                    "click .deletebutton" : "deleteQuestion",
                    "submit form" : "nullaction",
                    "change input[name=productquestion]" : "changeproductquestion"
                },
                render: function() {
                    console.log("Call to PartPersonalizationQuestionView");
                    console.log(this.model);
                    console.log("Question");
                    console.log(this.model);
                    $(this.el).html(this.template({
                        'readonly' : this.options.readonly,
                        "question" : this.model.get('question'),
                        "productquestion" : this.model.get("productquestion")
                    }));

                    setTimeout(_.bind(function() {
                        // do the existing answers..
                        var matches = _.filter(app.PartQuestionAnswerCollection.models, function(x) {
                            return x.get('partquestion_id') == this.model.get('partquestion_id');
                        }, this);
                        for (var i = 0; i < matches.length; i++) {
                            var x = matches[i];
                            this.$(".answerdiv").append((new PartPersonalizationQuestionAnswerView({model: x, revisionset_id : this.options.part.get('revisionset_id'), part_id : this.model.get("part_id"), partquestion_id : this.model.get('partquestion_id'), readonly: this.options.readonly})).render().el);
                        };

                        // add the blank add answer
                        if (!this.options.readonly) {
                            this.$(".answerdiv").append((new PartPersonalizationCreateNewAnswerView({model: this.model, revisionset_id : this.options.part.get('revisionset_id'), part_id : this.model.get("part_id"), partquestion_id : this.model.get('partquestion_id')})).render().el);
                        }
                    }, this));

                    return this;
                }
            });


            // this just takes a single text input and adds it as a question...
            window.PartPersonalizationNewQuestionView = Backbone.View.extend({
                template: _.template($("#PartPersonalizationNewQuestionView").html()),
                events: {
                    "click .addbutton" : "addNewQuestion",
                    "submit form" : "addNewQuestion"
                },
                initialize: function() {
                    _.bindAll(this, "render", "addNewQuestion");
                },
                addNewQuestion: function(e) {
                    e.preventDefault();

                    // fetch the question...
                    var question = this.$("input[name='question']").val();

                    // check that the question doesn't already exist for this part...
                    if (_.find(app.PartQuestionCollection.models, function(x) {
                            return (x.get('question').toLowerCase() == question.toLowerCase()) &&
                                (x.get('part_id') == this.model.get('part_id'));
                        }, this)) {
                        alert("Sorry, that question already exists for this part.");
                    } else {
                        showLoading("Adding question...");
                        $.ajax({
                            "url" : "/adminproduct/addQuestion/<?php echo $part_id; ?>/" + this.model.get("part_id"),
                            "type" : "POST",
                            "dataType" : "json",
                            "data": {
                                "question" : question
                            },
                            "success" : _.bind(function(data) {
                                if (data.success) {
                                    showGritter("Success", data.success_message);

                                    // update the collection
                                    app.PartQuestionCollection.add(data.data.data); // this is not a complete collection, but just for this part...
                                } else {
                                    // error...
                                    showGritter("Error", data.error_message);
                                }
                            }, this),
                            "complete" : function() {
                                hideLoading();
                            }
                        });
                    }
                },
                render: function() {
                    // stub!
                    $(this.el).html(this.template());
                    return this;
                }
            });

            window.PartPersonalizationView = Backbone.View.extend({
                className: "PartPersonalizationView",
                template: _.template($("#PartPersonalizationView").html()),
                initialize: function(options) {
                    this.options = options || {};
                    _.bindAll(this, "render", "add");
                    this.model.set("readonly", this.options.readonly);
                    app.PartQuestionAnswerPartVariationCollection.bind("reset", this.render);
                    app.PartVariationCollection.bind("reset", this.render);
                    app.PartQuestionAnswerCollection.bind("reset", this.render);
                    app.PartQuestionCollection.bind("reset", this.render);
                    app.PartQuestionCollection.bind("add", this.render);
                },
                add: function(x) {
                    this.$(".questions").append((new PartPersonalizationQuestionView({ model: x, part: this.model, readonly: this.options.readonly })).render().el);
                },
                render: function() {
                    //console.log("Call to PartPersonalizationView");
                    //console.log(this.model);
                    $(this.el).html(this.template(this.model.toJSON()));

                    // extract the questions
                    var myQuestions = app.PartQuestionCollection.models;

                    if (myQuestions.length > 0) {
                        _.each(myQuestions, _.bind(function(x) {
                            this.$(".questions").append((new PartPersonalizationQuestionView({ model: x, part: this.model, readonly: this.options.readonly })).render().el);
                        }, this));
                    }

                    // finally, add the new question view...
                    if (!this.options.readonly) {
                        $(this.el).append((new PartPersonalizationNewQuestionView({model: this.model})).render().el);
                    }

                    return this;
                }
            });


            var part_id = <?php echo $part_id; ?>;
            var part_model = new PartModel(<?php echo $product; ?>);

            var app;
            // build this thing
            app = {};
            <?php foreach (array("KnownModelCollection", "PartQuestionCollection", "PartQuestionAnswerCollection", "PartQuestionAnswerPartVariationCollection", "PartVariationCollection", "PartQuestionAnswerFitmentCollection", "DistributorCollection", "PartNumberCollection", "PartPartNumberCollection", "PartNumberPartQuestionCollection", "PartNumberModelCollection") as $c): ?>
            app.<?php echo $c; ?> = new <?php echo $c; ?>(<?php echo json_encode($$c); ?>);
            <?php endforeach; ?>

            var MainPartPersonalizationView;

            $(window).on("load", function() {
                MainPartPersonalizationView = new PartPersonalizationView({
                    model: part_model
                });
                // This is a very neutered App Router. It's just a carrier.
                $(".pagecontents").html(MainPartPersonalizationView.render().el);
            });

        </script>


        <div class="pagecontents">
            <strong>Loading...</strong>
        </div></div>


    </div>
</div>