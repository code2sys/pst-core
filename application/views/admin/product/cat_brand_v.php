<?php

$not_is_new = !isset($new) || !$new;

?>
<?php if ($not_is_new && $product["mx"] == 0): ?>
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

<script type="text/template" id="QuestionView">
    <div style="clear: both"></div>
<div class="noedit">
    <p><strong><span class="question"><%= obj.question %></span></strong> <button type="button" name="editButton" class="editButton">Edit</button><button type="button" name="removeButton" class="removeButton">Remove</button></p>
</div>
<div class="edit">
    <input type="text" name="question" size="40" maxlength="64" /> <button type="button" name="saveButton" class="saveButton">Save Changes</button><button type="button" name="cancelButton" class="cancelButton">Cancel Changes</button>
</div>

<table>
    <thead>
        <tr>
            <th>Answer</th>
            <th>Distributor</th>
            <th>Distributor Part #</th>
            <th>Manufacturer Part #</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="answers"></tbody>
</table>

    <p>&nbsp;</p>
</script>
<script type="text/template" id="AnswerView">
<td><div class="noansweredit"><span class="answer"><%= obj.answer %></span></div><div class="answeredit"><input type="text" name="answer" value="" size="40" maxlength="64"/></div></td>
<td><%= obj.name %></td>
<td><%= obj.part_number %></td>
<td><%= obj.manufacturer_part_number %></td>
    <td><div class="noansweredit"><button type="button" name="editAnswerButton">Edit</button><button type="button" name="removeAnswerButton">Remove</button></div><div class="answeredit"><button type="button" name="saveAnswerButton">Save Changes</button><button type="button" name="cancelAnswerButton">Cancel Changes</button></div></td>
</script>
<script type="text/template" id="AddView">
<p><strong>Add a New Filter Question/Answer</strong></p>

<?php if (count($distributor_part) == 0): ?>
    <p><em>Please add some distributor parts to this part entry under &quot;SKUs, Quantities, and Personalization&quot; before adding new filter questions.</em></p>
<?php else: ?>

<form onSubmit="return false; ">

<table>
    <tr>
        <td><strong>Question:</strong></td>
        <td><input type="text" size="64" maxlength="64" name="question" /></td>
    </tr>
    <tr>
        <td><strong>Answer:</strong></td>
        <td><input type="text" size="64" maxlength="64" name="answer" /></td>
    </tr>
    <tr>
        <td><strong>Distributor Part:</strong></td>
        <td><select name="partnumber_id">
                <?php if (count($distributor_part) > 0): ?>
                <option value="">-- Select Distributor Part --</option>
                <?php endif; ?>
                <?php foreach ($distributor_part as $part): ?>
                <option value="<?php echo $part["partnumber_id"]; ?>"><?php echo htmlentities($part["name"]); ?> <?php echo htmlentities($part["part_number"]); ?></option>
                <?php endforeach; ?>
            </select></td>
    </tr>
    <tr>
        <td colspan="2"><button class="addFunction">Add Filter Question/Answer</button></td>
    </tr>
</table>

</form>

    <?php endif; ?>

</script>
<script type="application/javascript">

    var QuestionViewIndex = {};

    var DistributorPartIndex = {};

    <?php foreach ($distributor_part as $part): ?>
    DistributorPartIndex[<?php echo $part["partnumber_id"]; ?>] = {
        "name" : "<?php echo addslashes($part["name"]); ?>",
        "part_number" : "<?php echo addslashes($part["part_number"]); ?>",
        "manufacturer_part_number" : "<?php echo addslashes($part["manufacturer_part_number"]); ?>"
    };
    <?php endforeach; ?>

    /*
        This is a little widget that looks for the question, answer, and then part. You have to choose the part from a drop-down, so we are going to need to have a list of available part variations.
     */
    window.AddView = Backbone.View.extend({
        template: _.template($("#AddView").text()),
        className: "AddView",
        initialize : function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "addFunction");
        },
        render: function() {
            $(this.el).html(this.template({}));
            return this;
        },
        events: {
            "click .addFunction" : "addFunction"
        },
        addFunction: function(e) {
            console.log(e);
            if (e) {
                e.preventDefault();
            }

            var question = this.$("input[name='question']").val();
            if (question == '') {
                alert('Please provide a question.');
                return;
            }

            var answer = this.$("input[name='answer']").val();
            if (answer == '') {
                alert('Please provide an answer.');
                return;
            }

            var partnumber_id = this.$("select[name=partnumber_id]").val();
            if (partnumber_id == '') {
                alert('Please select a distributor part number.');
                return;
            }

            partnumber_id = parseInt(partnumber_id, 10);

            // now, we need to create a function that registers it locally
            $.ajax({
                type: "POST",
                url : "/adminproduct/ajax_product_question_answer_add/<?php echo $part_id; ?>",
                data: {
                    "question" : question,
                    "answer" : answer,
                    "partnumber_id" : partnumber_id
                },
                dataType: "json",
                success: _.bind(function(response) {
                    console.log(response);
                    if (response.success) {
                        // add it!
                        registerNewQuestionView(response.data,partquestion_id, question, response.data.partnumberpartquestion_id, answer, DistributorPartIndex[partnumber_id].part_number, DistributorPartIndex[partnumber_id].manufacturer_part_number, DistributorPartIndex[partnumber_id].name);
                    } else {
                        // do something with this error.
                        alert(response.error_message);
                        window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                    }
                }, this),
                error: function() {
                    alert("An error occurred; reloading page.");
                    window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                }
            });
        }
    });


    /*
        This is a row - it has edit and remove functionality - it shows the answer (which is the editable part), part number, distributor, and manufacturer part #
     */
    window.AnswerView = Backbone.View.extend({
        template: _.template($("#AnswerView").text()),
        className: "AnswerView",
        tagName: "tr",
        initialize : function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "editButton", "cancelButton", "saveButton", "removeButton", "setAnswer");
        },
        setAnswer: function(a) {
            this.options.answer.answer = a;
            this.$("input[type=text]").val(this.options.answer.answer);
            this.$("span.answer").text = this.options.answer.answer;
        },
        render: function() {
            $(this.el).html(this.template(this.options.answer));
            this.cancelButton();
            return this;
        },
        events: {
            "click .editAnswerButton" : "editButton",
            "click .cancelAnswerButton" : "cancelButton",
            "click .saveAnswerButton" : "saveButton",
            "click .removeAnswerButton" : "removeButton"
        },
        editButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            this.$("input[type=text]").val(this.options.answer.answer);
            this.$(".answeredit").show();
            this.$(".noansweredit").hide();
        },
        cancelButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            this.$(".noansweredit").show();
            this.$(".answeredit").hide();
        },
        saveButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            this.options.answer.answer = this.$("input[type-text]").val();

            $.ajax({
                type: "POST",
                url : "/adminproduct/ajax_product_question_answer_update/<?php echo $part_id; ?>/" + this.options.answer.partquestion_id + "/" + this.options.answer.partnumberpartquestion_id,
                data: {
                    "answer" : this.options.answer.answer
                },
                dataType: "json",
                success: _.bind(function(response) {
                    console.log(response);
                    if (response.success) {
                        this.$("span.answer").text = this.options.answer.answer;
                        this.cancelButton();
                    } else {
                        // do something with this error.
                        alert(response.error_message);
                        window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                    }
                }, this),
                error: function() {
                    alert("An error occurred; reloading page.");
                    window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                }
            });

        },
        removeButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            if (confirm('Really remove this answer?')) {
                $.ajax({
                    type: "POST",
                    url: "/adminproduct/ajax_product_question_answer_remove/<?php echo $part_id; ?>/" + this.options.answer.partquestion_id + "/" + this.options.answer.partnumberpartquestion_id,
                    data: {},
                    dataType: "json",
                    success: _.bind(function (response) {
                        console.log(response);
                        if (response.success) {
                            $(this.el).remove();
                            delete(QuestionViewIndex[this.options.answer.partquestion_id]["answers"][this.options.answer.partnumberpartquestion_id]);
                        } else {
                            // do something with this error.
                            alert(response.error_message);
                            window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                        }
                    }, this),
                    error: function () {
                        alert("An error occurred; reloading page.");
                        window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                    }
                });
            }

        }
    });


    /*
    This is the question - it has edit and remove functionality - and it has a list of rows
     */
    window.QuestionView = Backbone.View.extend({
        template: _.template($("#QuestionView").text()),
        className: "QuestionView",
        initialize : function(options) {
            this.options = options || {};
            _.bindAll(this, "render", "editButton", "cancelButton", "saveButton", "removeButton", "addAnswerView");
        },
        render: function() {
            $(this.el).html(this.template(this.options.question));
            this.cancelButton();
            return this;
        },
        addAnswerView: function(v) {
            this.$(".answers").append(v.render().el);
        },
        events: {
            "click .editButton" : "editButton",
            "click .cancelButton" : "cancelButton",
            "click .saveButton" : "saveButton",
            "click .removeButton" : "removeButton"
        },
        editButton: function(e) {
            console.log("Call to editButton");
            if (e) {
                ;
                e.preventDefault();
            }

            this.$("input[type=text]").val(this.options.question.question);
            this.$(".edit").show();
            this.$(".noedit").hide();
            console.log("Call to editButton done.");
        },
        cancelButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            if (e) {
                ;
                e.preventDefault();
            }

            this.$(".noedit").show();
            this.$(".edit").hide();
        },
        saveButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }


            var question = this.$("input[name='question']").val();
            if (question == '') {
                alert('Please provide a question.');
                return;
            }

            $.ajax({
                type: "POST",
                url : "/adminproduct/ajax_product_question_update/<?php echo $part_id; ?>/" + this.options.question.partquestion_id,
                data: {
                    question: question
                },
                dataType: "json",
                success: _.bind(function(response) {
                    console.log(response);
                    if (response.success) {
                        this.options.question.question = question;
                        this.$("span.question").text = this.options.question.question;
                        this.cancelButton();
                    } else {
                        // do something with this error.
                        alert(response.error_message);
                        window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                    }
                }, this),
                error: function() {
                    alert("An error occurred; reloading page.");
                    window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                }
            });

        },
        removeButton: function(e) {
            if (e) {
                ;
                e.preventDefault();
            }

            if (confirm("Are you sure you want to remove this question and all answers associated with it?")) {

                $.ajax({
                    type: "POST",
                    url: "/adminproduct/ajax_product_question_remove/<?php echo $part_id; ?>/" + this.options.question.partquestion_id,
                    data: {},
                    dataType: "json",
                    success: _.bind(function (response) {
                        console.log(response);
                        if (response.success) {
                            $(this.el).remove();
                            delete(QuestionViewIndex[this.options.question.partquestion_id]);
                        } else {
                            // do something with this error.
                            alert(response.error_message);
                            window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                        }
                    }, this),
                    error: function () {
                        alert("An error occurred; reloading page.");
                        window.location.href = "/adminproduct/product_category_brand/<?php echo $part_id; ?>/" + (new Date()).getTime();
                    }
                });
            }
        }
    });


    window.registerNewQuestionView = function(partquestion_id, question, partnumberpartquestion_id, answer, part_number, manufacturer_part_number, name) {
        partquestion_id = parseInt(partquestion_id, 10);
        partnumberpartquestion_id = parseInt(partnumberpartquestion_id, 10);

        // OK, we need to see if there's a view
        if (!QuestionViewIndex[partquestion_id]) {
            // OK, there is no entry for this question...
            QuestionViewIndex[partquestion_id] = {
                partquestion_id : partquestion_id,
                question: question,
                answers : {}
            };

            // OK, we have to add a QuestionView
            QuestionViewIndex[partquestion_id]["view"] = new QuestionView({ question: {
                question: question,
                    partquestion_id : partquestion_id
                }

            });

            // We have to render this into the right spot on the screen...
            $(".filterquestionholder").append(QuestionViewIndex[partquestion_id]["view"].render().el);
        }

        // OK, you have to add the answer view...
        if (!QuestionViewIndex[partquestion_id]["answers"][partnumberpartquestion_id]) {
            QuestionViewIndex[partquestion_id]["answers"][partnumberpartquestion_id] = {
                partnumberpartquestion_id : partnumberpartquestion_id,
                answer: answer,
                part_number : part_number,
                manufacturer_part_number : manufacturer_part_number,
                name: name,
                partquestion_id : partquestion_id,
                question: question
            };
            QuestionViewIndex[partquestion_id]["answers"][partnumberpartquestion_id]["view"] = new AnswerView({
                answer: {
                    partnumberpartquestion_id : partnumberpartquestion_id,
                    answer: answer,
                    part_number : part_number,
                    manufacturer_part_number : manufacturer_part_number,
                    name: name,
                    partquestion_id : partquestion_id,
                    question: question
                }
            });

            // and you need to add it so it shows...
            QuestionViewIndex[partquestion_id].view.addAnswerView(QuestionViewIndex[partquestion_id]["answers"][partnumberpartquestion_id].view);

        } else {
            QuestionViewIndex[partquestion_id]["answers"][partnumberpartquestion_id].view.setAnswer(answer);
        }
    };


</script>

<?php endif; ?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cube"></i>&nbsp;<?php if (@$new): ?>New<?php else: ?>Edit<?php endif; ?> Product<?php if ($not_is_new): ?>: <?php echo $product['name']; ?><?php endif; ?></h1>

        <?php if ($not_is_new && $product["mx"] == 0): ?>
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
            "tag" => "product_category_brand"
        ), true);
        ?>
        <!-- END TABS -->

        <?php if ($product["mx"] == 0): ?>
        <form class="form_standard" method="POST" action="<?php echo base_url("adminproduct/product_category_brand_save/" . $part_id); ?>">

            <div class="tab_content">



                <div class="hidden_table">

                    <table width="100%" cellpadding="6">
                        <tr>
                            <td><b>Select Brand:</b></td>
                            <td colspan=2 style="width:85%;"><?php echo form_dropdown('manufacturer', $manufacturers, array_key_exists("name", $product_brand) ? $product_brand["name"] : "", ''); ?></td>
                        </tr>
                        <tr>
                            <td><b>OR</b></td>
                        </tr>
                        <tr>
                            <td><b>New Brand:</b></td>
                            <td colspan=2 style="width:85%;"><?php echo form_input(array('name' => 'new_manufacturer',
                                    'value' => "",
                                    'class' => 'text large',
                                    'placeholder' => 'New Brand')); ?></td>
                        </tr>
                        <tr>
                            <td><b>Product Categories:</b><br/>Please sepcify categories, one per line, using the complete category name, e.g. "Cat > Sub-Cat > Sub-Sub-Cat". <strong>Use a new line or a semicolon to separate multiple categories.</strong></td>
                            <td><?php echo form_textarea(array('name' => 'categories',
                                    'value' => implode("\n", array_map(function($x) {
                                        return $x["long_name"];
                                    }, $product_categories)),
                                    'cols' => 80,
                                    'rows' => 10,
                                    'placeholder' => 'Product Categories'), '', " style='width: auto;' "); ?></td>
                                    <td valign="top">
                                    <button type="button" id="searchbutton"><i class="fa fa-search"></i>&nbsp;Search Available Categories</button>
                                    </td>
                        </tr>
                        <tr style="display: none" id="category_table_row">
                                    <td></td>
                                    <td colspan="2" id="category_table_cell"><table id="category_table" style="width: 100%"></table></td>
                        </tr>
                    </table>
                    
                </div>


                <br>
                <!-- SUBMIT PRODUCT -->
                <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Categories and Brand</button>

        </form>

        <div style="clear: both"></div>

        <p>&nbsp;</p>

        <p><strong>Filter Questions</strong></p>


        <div class="filterquestionholder"></div>



        <div style="clear: both"></div>
            </div>


<script type="application/javascript">
var existingCategories = <?php echo json_encode($existingCategories); ?> 
var existingCategoriesArray = null;
var categoryIdMap = {};

(function() {
    $(document).on("ready", function() {
        existingCategoriesArray = [];
        for (var i = 0; i < existingCategories.length; i++) {
            var id = existingCategories[i].category_id;
            var long_name = existingCategories[i].long_name;
            categoryIdMap[id] = long_name;
            existingCategoriesArray.push(["<a href='#' class='addCategoryButton' data-categoryid='" + id + "'><i class='fa fa-plus'></i>&nbsp;Add</a>", long_name]);
        }

        // initialize the table...
        $("#category_table").DataTable({
            data: existingCategoriesArray,
            deferRender: true,
            columns : [
                { title: "Action"},
                { title: "Category"}
            ]
        });

        <?php if ($not_is_new && $product["mx"] == 0): ?>
        var fqh = $(".filterquestionholder");
        // Initialize all the questions we currently have
        <?php foreach ($product_questions as $pq): ?>
        <?php foreach ($pq["partvariations"] as $pv): ?>
        registerNewQuestionView(<?php echo $pq['partquestion_id']; ?>, "<?php echo addslashes($pv["question"]); ?>", <?php echo $pv["partnumberpartquestion_id"]; ?>, "<?php echo addslashes($pv["answer"]); ?>", "<?php echo addslashes($pv["part_number"]); ?>", "<?php echo addslashes($pv["manufacturer_part_number"]); ?>", "<?php echo addslashes($pv["name"]); ?>");
        <?php endforeach; ?>
        <?php endforeach; ?>

        // Add a spot to add a question...
        fqh.append(new AddView().render().el);
        <?php endif; ?>
    });

    $("#searchbutton").on("click", function(e) {
        e.preventDefault();
        $("#searchbutton").hide();
        $("#category_table_row").show();
    });

    $(document).on("click", ".addCategoryButton", function(e) {
        e.preventDefault();

        // Now, you have to figure out what the category is, and you have to add it...
        var id = e.target.dataset.categoryid;
        $("textarea[name='categories']").val($("textarea[name='categories']").val() + "\n" + categoryIdMap[id]);

        // finally, destroy the table and hide this and show the button
        $("#searchbutton").show();
        $("#category_table_row").hide();
    });
})();
</script>





            <?php else: ?>
        <div class="tab_content">
            <p><strong>Brand: </strong> <?php echo array_key_exists("name", $product_brand) ? $product_brand["name"] : ""; ?></p>
            <p><strong>Categories:</strong> </p>
            <ul>
                <?php foreach ($product_categories as $c): ?>
                <li><?php echo $c["long_name"]; ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($product_questions) > 0): ?>
            <p><strong>Filter Questions</strong></p>

            <?php foreach ($product_questions as $pq): ?>
                <p><em><?php echo htmlentities($pq["question"]); ?></em></p>

                <table>
                    <thead>
                    <tr>
                        <th>Answer</th>
                        <th>Distributor</th>
                        <th>Part Number</th>
                        <th>Manufacturer Part Number</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pq["partvariations"] as $pv): ?>
                    <tr>
                        <td><?php echo $pv["answer"]; ?></td>
                        <td><?php echo $pv["name"]; ?></td>
                        <td><?php echo $pv["part_number"]; ?></td>
                        <td><?php echo $pv["manufacturer_part_number"]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                    <p>&nbsp;</p>

            <?php endforeach?>

            <?php endif; ?>



        </div>
            <?php endif;?>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
