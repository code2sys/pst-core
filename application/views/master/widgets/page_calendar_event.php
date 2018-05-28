
<link href='/assets/css_front/fullcalendar.min.css' rel='stylesheet' />
<link href='/assets/css_front/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='/assets/js_front/moment.min.js'></script>
<script src='/assets/js_front/fullcalendar.min.js'></script>
<script type="application/javascript">

    window.getTruePosition = function (el) {
        var xPos = 0;
        var yPos = 0;

        while (el) {
            if (el.tagName == "BODY") {
                // deal with browser quirks with body/window/document and page scroll
                var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
                var yScroll = el.scrollTop || document.documentElement.scrollTop;

                xPos += (el.offsetLeft - xScroll + el.clientLeft);
                yPos += (el.offsetTop - yScroll + el.clientTop);
            } else {
                // for all other non-BODY elements
                xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
                yPos += (el.offsetTop - el.scrollTop + el.clientTop);
            }

            el = el.offsetParent;
        }
        return {
            x: xPos,
            y: yPos
        };
    };

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
                console.log([calEvent]);
                // fill  them in
                $("#hover_box .title").text(calEvent.title);
                // position it...
                var offset = window.getTruePosition(jsEvent.target);
                var parentOffset = window.getTruePosition(document.getElementById("page_calendar_widget_holder"));

                var hb = document.getElementById("hover_box");
                hb.style.left = (offset.x - parentOffset.x) + "px";
                hb.style.top = (offset.y - parentOffset.y + 24) + "px";

                // address
                var address1 = calEvent.address1;
                var address2 = calEvent.address2;
                var city = calEvent.city;
                var state = calEvent.state;
                var zip = calEvent.zip;

                if ((address1 && address1 != '') || (address2 && address2 != '') || (city && city != '') || (address1 && address1 != '') || (state && state != '') || (zip && zip != '') ) {

                    var leading = false;

                    if (address1 && address1 != '') {
                        $("#hover_box .where .address1").html(address1);
                        leading = true;
                    } else {
                        $("#hover_box .where .address1").html("");
                    }
                    if (address2 && address2 != '') {
                        $("#hover_box .where .address2").html((leading ? ", " : "") + address2);
                        leading = true;
                    } else {
                        $("#hover_box .where .address2").html("");
                    }
                    if (city && city != '') {
                        $("#hover_box .where .city").html((leading ? ", " : "") + city);
                        leading = true;
                    } else {
                        $("#hover_box .where .city").html("");
                    }
                    if (state && state != '') {
                        $("#hover_box .where .state").html((leading ? ", " : "") + state);
                        leading = true;
                    } else {
                        $("#hover_box .where .state").html("");
                    }
                    if (zip && zip != '') {
                        $("#hover_box .where .zip").html((leading ? " " : "") + zip);
                        leading = true;
                    } else {
                        $("#hover_box .where .zip").html("");
                    }


                    $("#hover_box .where").show();
                } else {
                    $("#hover_box .where").hide();
                }

                // when...

                $("#hover_box").show();
            },
            eventMouseout: function(calEvent, jsEvent, view) {
                $("#hover_box").hide();
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
<div class="page_calendar_widget_holder" id="page_calendar_widget_holder">
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
<div id='calendar<?php echo $page_section_id;?>'></div>
</div>
<style>
    .page_calendar_widget_holder {
        position: relative;
    }
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
    #hover_box .title {
        font-size: 16px;
        font-weight: bold;
    }

    #hover_box .when,
    #hover_box .where {
        text-indent: -10px;
        margin-left: 10px
    }
</style>