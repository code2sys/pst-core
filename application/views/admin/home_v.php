<?php
$lastYearOrder = $totalOrders[(date('Y') - 1)] > 0 ? $totalOrders[(date('Y') - 1)] : 1;
$orderPer = ($totalOrders[date('Y')] * 100) / $lastYearOrder;

$lastYearRevenue = $totalRevenue[(date('Y') - 1)] > 0 ? $totalRevenue[(date('Y') - 1)] : 1;
$revenuePer = ($totalRevenue[date('Y')] * 100) / $lastYearRevenue;
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-dashboard"></i>&nbsp;Dashboard</h1>
        <h3>Welcome to your admin panel</h3>
        <br>
        <?php if($dashboard || $_SESSION['userRecord']['admin']) { ?>

        <div class="dash-sec-1">
            <div class="container" style="min-height: 220px;">
                <div class="col-total">
                    <div class="total-1" last_year="<?php echo $ytdOrderCountLastYear; ?>" this_year="<?php echo $ytdOrderCountThisYear; ?>">
                        <p class="main">TOTAL ORDERS YTD</P>
                        <p class="grow"><?php if ($ytdOrderCountLastYear > 0 && $ytdOrderCountLastYear != $ytdOrderCountThisYear): ?><i class="fa fa-caret-<?php if ($ytdOrderCountLastYear < $ytdOrderCountThisYear): ?>up<?php else: ?>down<?php endif; ?>" aria-hidden="true"></i><?php echo number_format(100 * ($ytdOrderCountThisYear - $ytdOrderCountLastYear) / (1.0 * $ytdOrderCountLastYear), 0); ?>%<?php endif; ?></P>
                    </div>
                    <div class="total-2">
                        <p class="main1"><i class="fa fa-shopping-cart" aria-hidden="true"></i></P>
                        <p class="grow1"><?php echo number_format($ytdOrderCountThisYear, 0); ?></P>
                    </div>
                    <div class="total-3">
                        <p class="main2"><a href="<?php echo site_url('admin/orders'); ?>" style="color:white;">View More...</a></P>
                    </div>

                </div>
                <div class="col-total">
                    <div class="total-1" last_year="<?php echo $ytdRevenueLastYear; ?>" this_year="<?php echo $ytdRevenueThisYear; ?>">
                        <p class="main">TOTAL SALES YTD</P>
                        <p class="grow"><?php if ($ytdRevenueLastYear > 0 && $ytdRevenueLastYear != $ytdRevenueThisYear): ?><i class="fa fa-caret-<?php if ($ytdRevenueLastYear < $ytdRevenueThisYear): ?>up<?php else: ?>down<?php endif; ?>" aria-hidden="true"></i><?php echo number_format(100 * ($ytdRevenueThisYear - $ytdRevenueLastYear) / (1.0 * $ytdRevenueLastYear), 0); ?>%<?php endif; ?></P>
                    </div>
                    <div class="total-2">
                        <p class="main1"><i class="fa fa-credit-card" aria-hidden="true"></i></P>
                        <p class="grow1"><?php echo number_format($ytdRevenueThisYear, 2); ?></P>
                    </div>
                    <div class="total-3">
                        <p class="main2"><a href="<?php echo site_url('admin/orders'); ?>" style="color:white;">View More...</a></P>
                    </div>
                </div>
                <div class="col-total">
                    <div class="total-1">
                        <p class="main">TOTAL CUSTOMERS</P>
                        <p class="grow"></P>
                    </div>
                    <div class="total-2">
                        <p class="main1"><i class="fa fa-user" aria-hidden="true"></i></P>
                        <p class="grow1"><?php echo $totalCustomers; ?></P>
                    </div>
                    <div class="total-3">
                        <p class="main2"><a href="<?php echo site_url('admin/customers'); ?>" style="color:white;">View More...</a></P>
                    </div>
                </div>
                <div class="col-total">
                    <div class="total-1">
                        <p class="main">PENDING REVIEWS</P>
                        <p class="grow"></P>
                    </div>
                    <div class="total-2">
                        <p class="main1"><i class="fa fa-comments" aria-hidden="true"></i></P>
                        <p class="grow1"><?php echo $totalReviews; ?></P>
                    </div>
                    <div class="total-3">
                        <p class="main2"><a href="<?php echo site_url('admin_content/reviews'); ?>" style="color:white;">View More...</a></P>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-sec-1">
            <div class="container">
                <div class="panel-heading">
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                            <i class="fa fa-calendar"></i> <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="chrt " data-cstm="daily"><a href="javascript:void(0);">Today</a></li>
                            <li class="chrt" data-cstm="weekly"><a href="javascript:void(0);">Last 7 Days</a></li>
                            <li class="chrt active" data-cstm="monthly"><a href="javascript:void(0);">Last 30 Days</a></li>
                            <li class="chrt" data-cstm="yearly"><a href="javascript:void(0);">Last 12 Months</a></li>
                        </ul>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Sales Analytics <span class="monthly labelspanners">Last 30 Days</span><span class="daily labelspanners">Today</span><span class="weekly labelspanners">Last 7 Days</span><span class="yearly labelspanners">Last 12 Months</span></h3>
                </div>
                <div id="flot-placeholder2" style="width:100%;height:500px;margin:0 auto" class="monthly chrt1"></div>
                <div id="flot-placeholder" style="width:100%;height:500px;margin:0 auto;" class="daily chrt1"></div>
                <div id="flot-placeholder1" style="width:100%;height:500px;margin:0 auto;" class="weekly chrt1"></div>
                <div id="flot-placeholder3" style="width:100%;height:500px;margin:0 auto;" class="yearly chrt1"></div>
            </div>
        </div>

        <!--  REVIEW BLOCK -->
        <div class="dash-sec-1">
            <div class="container">
            <!--<h1 style="text-align:left"><a href="<?php echo base_url() . 'part'; ?>">Search</a></h1>
            <div class="hidden_table">
                <table width="100%" cellpadding="6">
                    <tr>
                        <td><b>Search Term</b></td><td><b>Count</b></td><td><b>Sales</b></td>
                    </tr>
                </table>
            </div>-->
                <div class="table-section">
                    <div class="sarni">
                        <p class="table-head"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Latest Orders</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) { ?>
                                    <tr>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                        <td><?php echo $order['status']; ?></td>
                                        <td><?php echo date('m/d/Y', $order['order_date']); ?></td>
                                        <td><?php echo $order['sales_price']; ?></td>
                                        <td><a class="btn btn-info" href="<?php echo site_url('admin/order_edit/' . $order['order_id']); ?>"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--  END REVIEW BLOCK -->
	<?php } ?>

        <!--  SALES BLOCK -->

        <!--  END SALES BLOCK -->

    </div>

</div>
<style>
    .btn-info {
        color: #ffffff;
        background-color: #5bc0de;
        border-color: #39b3d7;
    }
    a:link {
        color: #ffffff;
        text-decoration: none;
    }
    .btn {
        display: inline-block;
        margin-bottom: 0;
        font-weight: normal;
        text-align: center;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        padding: 8px 13px;
        font-size: 12px;
        line-height: 1.42857143;
        border-radius: 3px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

<script>
    var todaysDataOrders = <?php
        $todays_data_orders = array();
        $todays_keys_orders = array();
        $todays_data_customers = array();
        $todays_data_dollars = array();

        for ($i = 0; $i < count($todaysData); $i++) {
            $todays_data_orders[] = array(
                $i, $todaysData[$i]["number_orders"]
            );
            $todays_data_customers[] = array(
                $i, $todaysData[$i]["distinct_customers"]
            );
            $todays_data_dollars[] = array(
                $i, round($todaysData[$i]["total_sales_dollars"], 2)
            );
            $todays_keys_orders[] = array(
                $i, date("g a", strtotime(sprintf("%04d-%02d-%02d %02d:00:00", $todaysData[$i]["year"], $todaysData[$i]["month"], $todaysData[$i]["day"], $todaysData[$i]["hour"])))
            );
        }

        echo json_encode($todays_data_orders, JSON_NUMERIC_CHECK);

        ?>;
    var todaysDataCustomers = <?php echo json_encode($todays_data_customers, JSON_NUMERIC_CHECK); ?>;
    var todaysDataDollars = <?php echo json_encode($todays_data_dollars, JSON_NUMERIC_CHECK); ?>;

    var sevenDaysDataOrders = <?php

        $sevenDays_data_orders = array();
        $sevenDays_keys_orders = array();
        $sevenDays_data_customers = array();
        $sevenDays_data_dollars = array();

        for ($i = 0; $i < count($sevenDaysData); $i++) {
            $sevenDays_data_orders[] = array(
                $i, $sevenDaysData[$i]["number_orders"]
            );
            $sevenDays_data_customers[] = array(
                $i, $sevenDaysData[$i]["distinct_customers"]
            );
            $sevenDays_data_dollars[] = array(
                $i, round($sevenDaysData[$i]["total_sales_dollars"], 2)
            );
            $sevenDays_keys_orders[] = array(
                $i, date("m-d", strtotime(sprintf("%04d-%02d-%02d 00:00:00", $sevenDaysData[$i]["year"], $sevenDaysData[$i]["month"], $sevenDaysData[$i]["day"])))
            );
        }
        echo json_encode($sevenDays_data_orders, JSON_NUMERIC_CHECK);


    ?>;
    var sevenDaysDataCustomers = <?php echo json_encode($sevenDays_data_customers, JSON_NUMERIC_CHECK); ?>;
    var sevenDaysDataDollars = <?php echo json_encode($sevenDays_data_dollars, JSON_NUMERIC_CHECK); ?>;

    var thirtyDaysData = <?php

        $thirtyDays_data_orders = array();
        $thirtyDays_keys_orders = array();
        $thirtyDays_data_customers = array();
        $thirtyDays_data_dollars = array();
        $last_month = 0;

        for ($i = 0; $i < count($thirtyDaysData); $i++) {
            $thirtyDays_data_orders[] = array(
                $i, $thirtyDaysData[$i]["number_orders"]
            );
            $thirtyDays_data_customers[] = array(
                $i, $thirtyDaysData[$i]["distinct_customers"]
            );
            $thirtyDays_data_dollars[] = array(
                $i, round($thirtyDaysData[$i]["total_sales_dollars"], 2)
            );
            if ($thirtyDaysData[$i]["month"] != $last_month) {
                $thirtyDays_keys_orders[] = array(
                    $i, date("m-d", strtotime(sprintf("%04d-%02d-%02d 00:00:00", $thirtyDaysData[$i]["year"], $thirtyDaysData[$i]["month"], $thirtyDaysData[$i]["day"])))
                );

                $last_month = $thirtyDaysData[$i]["month"];
            } else {
                $thirtyDays_keys_orders[] = array(
                    $i, date("d", strtotime(sprintf("%04d-%02d-%02d 00:00:00", $thirtyDaysData[$i]["year"], $thirtyDaysData[$i]["month"], $thirtyDaysData[$i]["day"])))
                );

            }
        }
        echo json_encode($thirtyDays_data_orders, JSON_NUMERIC_CHECK);


        ?>;
    var thirtyDaysDataCustomers = <?php echo json_encode($thirtyDays_data_customers, JSON_NUMERIC_CHECK); ?>;
    var thirtyDaysDataDollars = <?php echo json_encode($thirtyDays_data_dollars, JSON_NUMERIC_CHECK); ?>;

    var oneYearsData = <?php

        $oneYears_data_orders = array();
        $oneYears_keys_orders = array();
        $oneYears_data_customers = array();
        $oneYears_data_dollars = array();

        for ($i = 0; $i < count($oneYearsData); $i++) {
            $oneYears_data_orders[] = array(
                $i, $oneYearsData[$i]["number_orders"]
            );
            $oneYears_data_customers[] = array(
                $i, $oneYearsData[$i]["distinct_customers"]
            );
            $oneYears_data_dollars[] = array(
                $i, round($oneYearsData[$i]["total_sales_dollars"], 2)
            );
            $oneYears_keys_orders[] = array(
                $i, date("M y", strtotime(sprintf("%04d-%02d-%02d 00:00:00", $oneYearsData[$i]["year"], $oneYearsData[$i]["month"], $oneYearsData[$i]["day"])))
            );
        }
        echo json_encode($oneYears_data_orders, JSON_NUMERIC_CHECK);


        ?>;
    var oneYearsDataCustomers = <?php echo json_encode($oneYears_data_customers, JSON_NUMERIC_CHECK); ?>;
    var oneYearsDataDollars = <?php echo json_encode($oneYears_data_dollars, JSON_NUMERIC_CHECK); ?>;


    var options = {
        xaxis: {
            mode: "categories",
            ticks: <?php echo json_encode($todays_keys_orders); ?>,
            tickLength: 1
        },
        yaxes: [{
            position: "left",
            color: "blue",
            axisLabel: "#",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3,
            allowDecimals: false,
            minTickSize: 1
        }, {
            minTickSize: 25.00,
            position: "right",
            color: "red",
            axisLabel: "$",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3
        }],
        legend: {
            noColumns: 0,
            labelBoxBorderColor: "#000000",
            position: "nw"
        },
        grid: {
            hoverable: true,
            borderWidth: 2,        
            backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
        }
    };
    var options1 = {
        xaxis: {
            mode: "categories",
            ticks: <?php echo json_encode($sevenDays_keys_orders); ?>,
            tickLength: 1
        },
        yaxes: [{
            position: "left",
            color: "blue",
            axisLabel: "#",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3,
            allowDecimals: false,
            minTickSize: 1
        }, {
            minTickSize: 25.00,
            position: "right",
            color: "red",
            axisLabel: "$",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3
        }],
        legend: {
            noColumns: 0,
            labelBoxBorderColor: "#000000",
            position: "nw"
        },
        grid: {
            hoverable: true,
            borderWidth: 2,
            backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
        }
    };
    var options2 = {
        xaxis: {
            mode: "categories",
            ticks: <?php echo json_encode($thirtyDays_keys_orders); ?>,
            tickLength: 1
        },
        yaxes: [{
            position: "left",
            color: "blue",
            axisLabel: "#",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3,
            allowDecimals: false,
            minTickSize: 1
        }, {
            minTickSize: 25.00,
            position: "right",
            color: "red",
            axisLabel: "$",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3
        }],
        legend: {
            noColumns: 0,
            labelBoxBorderColor: "#000000",
            position: "nw"
        },
        grid: {
            hoverable: true,
            borderWidth: 2,
            backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
        }
    };
    var options3 = {
        xaxis: {
            mode: "categories",
            ticks: <?php echo json_encode($oneYears_keys_orders); ?>,
            tickLength: 1
        },
        yaxes: [{
            position: "left",
            color: "blue",
            axisLabel: "#",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3,
            allowDecimals: false,
            minTickSize: 1
        }, {
            minTickSize: 25.00,
            position: "right",
            color: "red",
            axisLabel: "$",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 3
        }],
        legend: {
            noColumns: 0,
            labelBoxBorderColor: "#000000",
            position: "nw"
        },
        grid: {
            hoverable: true,
            borderWidth: 2,
            backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
        }
    };

    $(document).ready(function () {
        $.plot($("#flot-placeholder"), [
            {
                label: " # Orders ",
                data: todaysDataOrders,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 1
                }
            },
            {
                label: " # Customers ",
                data: todaysDataCustomers,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 2
                }
            },
            {
                label: " $ Revenue ",
                data: todaysDataDollars,
                yaxis: 2,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 3
                }
            }
        ], options);
        $("#flot-placeholder").UseTooltip();
        
        $.plot($("#flot-placeholder1"), [
            {
                label: " # Orders ",
                data: sevenDaysDataOrders,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 1
                }
            },
            {
                label: " # Customers ",
                data: sevenDaysDataCustomers,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 2
                }
            },
            {
                label: " $ Revenue ",
                data: sevenDaysDataDollars,
                yaxis: 2,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 3
                }
            }
        ], options1);
        $("#flot-placeholder1").UseTooltip();
        
        $.plot($("#flot-placeholder2"), [
            {
                label: " # Orders ",
                data: thirtyDaysData,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 1
                }
            },
            {
                label: " # Customers ",
                data: thirtyDaysDataCustomers,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 2
                }
            },
            {
                label: " $ Revenue ",
                data: thirtyDaysDataDollars,
                yaxis: 2,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 3
                }
            }
        ], options2);
        $("#flot-placeholder2").UseTooltip();

        $.plot($("#flot-placeholder3"), [
            {
                label: " # Orders ",
                data: oneYearsData,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 1
                }
            },
            {
                label: " # Customers ",
                data: oneYearsDataCustomers,
                yaxis: 1,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 2
                }
            },
            {
                label: " $ Revenue ",
                data: oneYearsDataDollars,
                yaxis: 2,
                bars: {
                    show: true,
                    barWidth: 0.2,
                    order: 3
                }
            }
        ], options3);
        $("#flot-placeholder3").UseTooltip();
    });

    function gd(year, month, day) {
        return new Date(year, month, day).getTime();
    }

    var previousPoint = null, previousLabel = null;

    $.fn.UseTooltip = function () {
        $(this).bind("plothover", function (event, pos, item) {
            if (item) {
                if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                    previousPoint = item.dataIndex;
                    previousLabel = item.series.label;
                    $("#tooltip").remove();

                    var x = item.datapoint[0];
                    var y = item.datapoint[1];

                    var color = item.series.color;

                    //console.log(item.series.xaxis.ticks[x].label);                
                
                    showTooltip(item.pageX,
                    item.pageY,
                    color,
                    "<strong>" + item.series.label + "</strong><br>" + item.series.xaxis.ticks[x].label + " : <strong>" + y + "</strong>");                
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
    };

    function showTooltip(x, y, color, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 40,
            left: x - 120,
            border: '2px solid ' + color,
            padding: '3px',
            'font-size': '9px',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9
        }).appendTo("body").fadeIn(200);
    }
    jQuery('.chrt').on('click', function() {
        var cstm = jQuery(this).data('cstm');
        jQuery('.chrt').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.chrt1').hide();
        jQuery('.labelspanners').hide();
        jQuery('.'+cstm).show();
    });
    jQuery(document).ready(function() {
       jQuery('#flot-placeholder1').hide();
       jQuery('#flot-placeholder').hide();
       jQuery('#flot-placeholder3').hide();
        jQuery('.labelspanners').hide();
        jQuery('.labelspanners.monthly').show();
    });
</script>
<style>
    * {
        -webkit-box-sizing: inherit !important;
        -moz-box-sizing: inherit !important;
        box-sizing: inherit !important;
    }
</style>