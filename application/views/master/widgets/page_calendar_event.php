
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

                if ($x["start"] == $x["end"] || $x["end"] <= "0000-00-00 00:00:00") {
                    unset($x["end"]);
                }

                $x["extra_link"] = $x["url"];
                unset($x["url"]);

                return $x;
            }, array_values(array_filter($events, function($x) {
                return $x["start"] > "0000-00-00 00:00:00";
            })))); ?>,
            eventClick: function(calEvent, jsEvent, view) {
                console.log(["Click on event", calEvent, jsEvent, view]);
                $("#hover_box<?php echo $page_section_id;?>").hide();
                $("#page_calendar_modal<?php echo $page_section_id;?> .modal-body-title").html(calEvent.title);

                // description
                if (calEvent.description && calEvent.description != '') {
                    $("#page_calendar_modal<?php echo $page_section_id;?> .description").html(calEvent.description);
                } else {
                    $("#page_calendar_modal<?php echo $page_section_id;?> .description").html("");
                }

                // link
                if (calEvent.extra_link && calEvent.extra_link != '') {
                    $("#page_calendar_modal<?php echo $page_section_id;?> .extra_link a").attr("href", calEvent.extra_link);
                    $("#page_calendar_modal<?php echo $page_section_id;?> .extra_link a").text(calEvent.extra_link);
                    $("#page_calendar_modal<?php echo $page_section_id;?> .extra_link").show();
                } else {
                    $("#page_calendar_modal<?php echo $page_section_id;?> .extra_link").hide();
                }


                // where



                var address1 = calEvent.address1;
                var address2 = calEvent.address2;
                var city = calEvent.city;
                var state = calEvent.state;
                var zip = calEvent.zip;

                if ((address1 && address1 != '') || (address2 && address2 != '') || (city && city != '') || (address1 && address1 != '') || (state && state != '') || (zip && zip != '') ) {

                    var leading = false;

                    if (address1 && address1 != '') {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .address1").html(address1);
                        leading = true;
                    } else {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .address1").html("");
                    }
                    if (address2 && address2 != '') {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .address2").html((leading ? "<br/>" : "") + address2);
                        leading = true;
                    } else {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .address2").html("");
                    }
                    if (city && city != '') {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .city").html((leading ? "<br/>" : "") + city);
                        leading = true;
                    } else {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .city").html("");
                    }
                    if (state && state != '') {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .state").html((leading ? ", " : "") + state);
                        leading = true;
                    } else {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .state").html("");
                    }
                    if (zip && zip != '') {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .zip").html((leading ? " " : "") + zip);
                        leading = true;
                    } else {
                        $("#page_calendar_modal<?php echo $page_section_id;?> .where .zip").html("");
                    }


                    $("#page_calendar_modal<?php echo $page_section_id;?> .where").show();
                } else {
                    $("#page_calendar_modal<?php echo $page_section_id;?> .where").hide();
                }


                // when
                var when_string = calEvent.start.format("dddd, MMMM Do YYYY, h:mm a");
                if (calEvent.start != calEvent.end) {
                    if (calEvent.start.format("MMMM Do YYYY") != calEvent.end.format("MMMM Do YYYY")) {
                        when_string += " to " + calEvent.end.format("MMMM Do YYYY, h:mm a");
                    } else {
                        when_string += " to " + calEvent.end.format("h:mm:ss a");
                    }
                }
                $("#page_calendar_modal<?php echo $page_section_id;?> .when").html(when_string);

                setTimeout(function () {
                    $('#page_calendar_modal<?php echo $page_section_id;?>').modal('show');
                }, 500);
            },
            eventMouseover: function(calEvent, jsEvent, view) {
                console.log([calEvent]);
                // fill  them in
                $("#hover_box<?php echo $page_section_id;?> .title").text(calEvent.title);
                // position it...
                var offset = window.getTruePosition(jsEvent.target);
                var parentOffset = window.getTruePosition(document.getElementById("page_calendar_widget_holder<?php echo $page_section_id;?>"));

                var hb = document.getElementById("hover_box<?php echo $page_section_id;?>");
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
                        $("#hover_box<?php echo $page_section_id;?> .where .address1").html(address1);
                        leading = true;
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .where .address1").html("");
                    }
                    if (address2 && address2 != '') {
                        $("#hover_box<?php echo $page_section_id;?> .where .address2").html((leading ? "<br/>" : "") + address2);
                        leading = true;
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .where .address2").html("");
                    }
                    if (city && city != '') {
                        $("#hover_box<?php echo $page_section_id;?> .where .city").html((leading ? "<br/>" : "") + city);
                        leading = true;
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .where .city").html("");
                    }
                    if (state && state != '') {
                        $("#hover_box<?php echo $page_section_id;?> .where .state").html((leading ? ", " : "") + state);
                        leading = true;
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .where .state").html("");
                    }
                    if (zip && zip != '') {
                        $("#hover_box<?php echo $page_section_id;?> .where .zip").html((leading ? " " : "") + zip);
                        leading = true;
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .where .zip").html("");
                    }


                    $("#hover_box<?php echo $page_section_id;?> .where").show();
                } else {
                    $("#hover_box<?php echo $page_section_id;?> .where").hide();
                }

                // when...
                $("#hover_box<?php echo $page_section_id;?> .start").html(calEvent.start.format("dddd, MMMM Do YYYY, h:mm a"));
                if (calEvent.start != calEvent.end && calEvent.end) {
                    if (calEvent.start.format("MMMM Do YYYY") != calEvent.end.format("MMMM Do YYYY")) {
                        $("#hover_box<?php echo $page_section_id;?> .end").html(" to " + calEvent.end.format("MMMM Do YYYY, h:mm a"));
                    } else {
                        $("#hover_box<?php echo $page_section_id;?> .end").html(" to " + calEvent.end.format("h:mm:ss a"));
                    }
                } else {
                    // just the start...
                    $("#hover_box<?php echo $page_section_id;?> .end").hide();
                }

                $("#hover_box<?php echo $page_section_id;?>").show();
            },
            eventMouseout: function(calEvent, jsEvent, view) {
                $("#hover_box<?php echo $page_section_id;?>").hide();
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
<div class="page_calendar_widget_holder" id="page_calendar_widget_holder<?php echo $page_section_id;?>">
<div id="hover_box<?php echo $page_section_id;?>" style="display:none">
    <h1 class="title"></h1>

    <div class="when">
        <strong>When</strong> <br/>
        <span class="start"></span>
        <span class="end"></span>
    </div>
    <div class="where">
        <strong>Where</strong><br/>
        <span class="address1"></span><span class="address2"></span><span class="city"></span><span class="state"></span><span class="zip"></span>
    </div>

</div>
<div id='calendar<?php echo $page_section_id;?>'></div>
</div>

<div class="modal fade pop" id="page_calendar_modal<?php echo $page_section_id;?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body modal-body-calendar">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <p class="modal-body-title"></p>

                <p class="modal-body-subtitle when">Looking for Your Next Adventure?</p>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="">
                            <div class="description downspace">
                            </div>

                            <div class="where downspace">
                                <strong>Where</strong><br/>
                                <span class="address1"></span><span class="address2"></span><span class="city"></span><span class="state"></span><span class="zip"></span>
                            </div>
                            <div class="extra_link downspace">
                                <strong>More Information</strong><br/>
                                <a href="" target="_blank"></a>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

<style>
    .page_calendar_widget_holder {
        position: relative;
    }
    #hover_box<?php echo $page_section_id;?> {
        border-color: black;
        background-color: white;
        font-size: 12px;
        min-width: 200px;
        max-width: 80%;
        position: absolute;
        border: 2px solid gray;
        padding: 6px;
        z-index: 1000;

    }
    #hover_box<?php echo $page_section_id;?> .title {
        font-size: 16px;
        font-weight: bold;
    }

    #hover_box<?php echo $page_section_id;?> .when,
    #hover_box<?php echo $page_section_id;?> .where {
        text-indent: -10px;
        margin-left: 10px;
        padding-bottom: 12px;
    }

    #page_calendar_modal<?php echo $page_section_id;?> .downspace {
        padding-bottom: 12px;
    }

    #page_calendar_modal<?php echo $page_section_id;?> .modal-body-calendar {
        padding-bottom: 12px;
    }
    #page_calendar_modal<?php echo $page_section_id;?> .modal-body-calendar .modal-body-title {
        text-align: center;
        font-size: 150%;
        font-weight: bold;
    }
    #page_calendar_modal<?php echo $page_section_id;?> .modal-body-calendar .modal-body-subtitle {
        text-align: center;
        font-size: 125%;
        font-weight: bold;
    }
    #page_calendar_modal<?php echo $page_section_id;?> .modal-body-calendar .where,
    #page_calendar_modal<?php echo $page_section_id;?> .modal-body-calendar .extra_link,
    {
        padding-bottom: 12px;
    }
</style>