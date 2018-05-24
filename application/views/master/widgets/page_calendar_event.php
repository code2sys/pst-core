
<link href='/assets/css_front/fullcalendar.min.css' rel='stylesheet' />
<link href='/assets/css_front/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='/assets/js_front/moment.min.js'></script>
<script src='/assets/js_front/fullcalendar.min.js'></script>
<script type="application/javascript">

    $(document).ready(function() {

        $('#calendar<?php echo $page_section_id;?>').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
            },
            defaultDate: '<?php echo date("Y-m-d"); ?>',
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: <?php echo json_encode(array_map(function($x) {
                $x["id"] = $x["page_calendar_event_id"];
                $data = array(
                    "title" => $x["title"],
                    "start" => $x["start"],
                    "id" => $x["page_calendar_event_id"]
                );

                if ($x["start"] == $x["end"] || $x["end"] <= "0000-00-00 00:00:00") {
                    unset($x["end"]);
                }

                return $x;
            }, array_values(array_filter($events, function($x) {
                return $x["start"] > "0000-00-00 00:00:00";
            })))); ?>,
            eventClick: function(calEvent, jsEvent, view) {
                console.log(["Click on event", calEvent, jsEvent, view]);
                $("#hover_box").css("display", "none");
            },
            eventMouseover: function(calEvent, jsEvent, view) {
                console.log(["Hover on event", calEvent, jsEvent, view]);
                // fill  them in
                $("#hover_box .title").text(calEvent.title);
                // position it...

                // display them..
                var offset = $(jsEvent.currentTarget).offset();
                var width = $(jsEvent.currentTarget).width();
                console.log("Found offset");
                console.log(offset);

                $("#hover_box").offset(offset);
                console.log("Hoverbox offset");
                console.log($("#hover_box").offset());

                $("#hover_box").css("display", "block");
            },
            eventMouseout: function(calEvent, jsEvent, view) {
                console.log(["Out of the event", calEvent, jsEvent, view]);
                $("#hover_box").css("display", "none");
            }
        });

    });

</script>
<style>
    #calendar<?php echo $page_section_id;?> {
        max-width: 900px;
        margin: 0 auto;
    }

</style>
<div id='calendar<?php echo $page_section_id;?>'></div>
<div id="hover_box" style="display:none">
    <h1 class="title"></h1>

    <div class="when">
        <strong>When</strong> <br/>
        <span class="start"></span>
        <span class="end"></span>
    </div>
    <div class="where">
        <strong>Where</strong><br/>
        <span class="address1"></span>
        <span class="state"></span>
        <span class="city"></span>
        <span class="state"></span>
        <span class="zip"></span>
    </div>

</div>
<style>
    #hover_box {
        border-color: black;
        background-color: white;
        font-size: 12px;
        width: 200px;
        max-width: 80%;
        position: absolute;
        border: 2px solid gray;
        padding: 6px;
        z-index: 1000;

    }
    #hoverbox .title {
        font-size: 16px;
        font-weight: bold;
    }

    #hoverbox .when,
    #hoverbox .where {
        text-indent: -10px;
        margin-left: 10px
    }
</style>