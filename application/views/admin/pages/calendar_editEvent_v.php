<!-- JLB: I would like to strangle whoever required two versions of this -->
<script src="/assets/insourced/jquery-1.12.4.js"></script>
<script src="/assets/insourced/jquery-ui.js"></script>
<link rel="stylesheet" href="/assets/css_front/jquery.dataTables.min.css" type="text/css" >
<script src="/assets/js_front/jquery.dataTables.min.js"></script>
<script src="/assets/js_front/moment.js"></script>


<link rel="stylesheet" href="/assets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcore.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcolorpicker.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxradiobutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxdropdownbutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxscrollview.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxbuttons.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcalendar.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxtooltip.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/globalization/globalize.js"></script>


<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-dashboard"></i>&nbsp;Edit Page: Edit Calendar Event</h1>
        <a href="<?php echo base_url('pages/edit/' . $pageId); ?>" class="button" style="float:right; margin:-50px 0 0 100px;">Back to Page</a>
        <br>

    <div class="tab_content">
        <div class="hidden_table box-table-content">


            <form method="post" action="/pages/calendarSaveEvent/<?php echo $pageId; ?>/<?php echo $page_calendar_event_id; ?>" class="form_standard" id="edit_form">
                <table width="auto" cellpadding="12">
                    <tr>
                        <td valign="top"><strong>Title:*</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="title" value="<?php echo htmlentities($page_calendar_event["title"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Description:</strong></td>
                        <td valign="top"><textarea name="description" cols="80" rows="15"><?php echo htmlentities($page_calendar_event["description"]); ?></textarea></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Start Date/Time:*</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="start" class="enhancedDateSelector" id="add_form_start" value="<?php echo date('m/d/Y g:i a', $page_calendar_event["start"] > '0000-00-00 00:00:00' ? strtotime($page_calendar_event["start"]) : time()); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>End Date/Time:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="end" class="enhancedDateSelector"  id="add_form_end" value="<?php echo date('m/d/Y g:i a', $page_calendar_event["end"] > '0000-00-00 00:00:00' ? strtotime($page_calendar_event["end"]) : time()); ?>" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Additional Info Link URL:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="url" value="<?php echo htmlentities($page_calendar_event["url"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td colspan="2"><em>Location</em></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Address</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="address1" value="<?php echo htmlentities($page_calendar_event["address1"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Address 2</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="address2" value="<?php echo htmlentities($page_calendar_event["address2"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>City</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="city" value="<?php echo htmlentities($page_calendar_event["city"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>State</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="state" value="<?php echo htmlentities($page_calendar_event["state"]); ?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Zip</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="zip" value="<?php echo htmlentities($page_calendar_event["zip"]); ?>"/></td>
                    </tr>
                </table>
                <input type="submit" name="submit" value="Update Event">
            </form>
        </div>
    </div>

    <script type="application/javascript">
        $(document).ready(function() {
            $("#add_form_end").jqxDateTimeInput({
                "showTimeButton" : true,
                width: '350px',
                height: '25px',
                formatString: 'M/d/yyyy h:mm tt'
            });
            $("#add_form_start").jqxDateTimeInput({
                "showTimeButton" : true,
                width: '350px',
                height: '25px',
                formatString: 'M/d/yyyy h:mm tt'
            });

            $("#add_form_end").on("change", function(event) {

                $("#add_form_end_jqxDateTimeInput").val(event.args.date);
            });

            $("#add_form_start").on("change", function(event) {

                $("#add_form_start_jqxDateTimeInput").val(event.args.date);
            });

            $("#edit_form").on("submit", function(e) {
                // Is there a title?
                var title = $("#edit_form input[name=title]").val();

                if (!title || title === "") {
                    alert("Please provide a title.");
                    return false;
                }

                // Is there a date?
                var start = $("#edit_form input[name=start]").val();

                if (!start || start === "") {
                    alert("Please specify a start time.");
                    return false;
                }
                return true;
            })

        })
    </script>

    </div>
</div>

