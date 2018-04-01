<style>

    .scrolling_marquee {
        height: 50px;
        overflow: hidden;
        position: relative;
    }
    .scrolling_marquee h3 {
        font-size: 14px;
        font-weight: bold;
        color: #ec1d23;
        position: absolute;
        width: 100%;
        min-width: 800px;
        height: 100%;
        margin: 0;
        line-height: 50px;
        text-align: center;
        /* Starting position */
        -moz-transform:translateX(100%);
        -webkit-transform:translateX(100%);
        transform:translateX(100%);
        /* Apply animation to this element */
        -moz-animation: scrolling_marquee 30s linear infinite;
        -webkit-animation: scrolling_marquee 30s linear infinite;
        animation: scrolling_marquee 30s linear infinite;
    }

    @media screen and (max-width: 767px) {
        .scrolling_marquee h3 {
            -moz-animation: scrolling_marquee 20s linear infinite;
            -webkit-animation: scrolling_marquee 20s linear infinite;
            animation: scrolling_marquee 20s linear infinite;
        }
    }

    @media screen and (max-width: 400px) {
        .scrolling_marquee h3 {
            -moz-animation: scrolling_marquee 18s linear infinite;
            -webkit-animation: scrolling_marquee 18s linear infinite;
            animation: scrolling_marquee 18s linear infinite;
        }
    }


    /* Move it (define the animation) */
    @-moz-keyframes scrolling_marquee {
        0%   { -moz-transform: translateX(75%); }
        100% { -moz-transform: translateX(-75%); }
    }
    @-webkit-keyframes scrolling_marquee {
        0%   { -webkit-transform: translateX(75%); }
        100% { -webkit-transform: translateX(-75%); }
    }
    @keyframes scrolling_marquee {
        0%   {
            -moz-transform: translateX(75%); /* Firefox bug fix */
            -webkit-transform: translateX(75%); /* Firefox bug fix */
            transform: translateX(75%);
        }
        100% {
            -moz-transform: translateX(-75%); /* Firefox bug fix */
            -webkit-transform: translateX(-75%); /* Firefox bug fix */
            transform: translateX(-75%);
        }
    }


</style>
<div class='scrolling_marquee'><h3 style="color: <?php echo $store_header_marquee_color; ?>"><?php echo $store_header_marquee_contents; ?></h3></div>