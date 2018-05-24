
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
            },
            eventMouseover: function(calEvent, jsEvent, view) {
                console.log(["Hover on event", calEvent, jsEvent, view]);
            },
            eventMouseout: function(calEvent, jsEvent, view) {
                console.log(["Out of the event", calEvent, jsEvent, view]);
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
        <span class="start"></span>
        <span class="end"></span>
    </div>
    <div class="where">
        <span class="address1"></span>
        <span class="state"></span>
        <span class="city"></span>
        <span class="state"></span>
        <span class="zip"></span>
    </div>

</div>