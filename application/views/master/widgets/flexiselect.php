<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/16/18
 * Time: 9:03 AM
 *
 * Whoever did this was the dumbest fuck on the planet:
 * There's this flexiselDemo# code, where it has numbers 1-4.
 * As far as I can tell, only 3 really exists.
 *
 * This is yet another piece of code that breaks IE11 and causes the dealers to complain, because we have THREE FUCKING VERSIONS of jQuery on this site.
 *
 * Instead of using the version, or, God forbid, updating, they just grabbed another one.
 *
 */

?>
<script type="text/javascript">

    $(window).load(function() {
        try {
            $("#flexiselDemo1").flexisel();
        } catch(err) {
            console.log("Error in flexiselDemo: " + err);
        }

        try {
            $("#flexiselDemo2").flexisel({
                enableResponsiveBreakpoints: true,
                responsiveBreakpoints: {
                    portrait: {
                        changePoint: 480,
                        visibleItems: 1
                    },
                    landscape: {
                        changePoint: 640,
                        visibleItems: 2
                    },
                    tablet: {
                        changePoint: 768,
                        visibleItems: 3
                    }
                }
            });
        } catch(err) {
            console.log("Error in flexiselDemo2: " + err);
        }

        try {
            $("#flexiselDemo3").flexisel({
                visibleItems: 5,
                animationSpeed: 1000,
                autoPlay: true,
                autoPlaySpeed: 3000,
                pauseOnHover: true,
                enableResponsiveBreakpoints: true,
                responsiveBreakpoints: {
                    portrait: {
                        changePoint: 480,
                        visibleItems: 3
                    },
                    landscape: {
                        changePoint: 640,
                        visibleItems: 4
                    },
                    tablet: {
                        changePoint: 768,
                        visibleItems: 5
                    }
                }
            });
        } catch(err) {
            console.log("Error in flexiselDemo3: " + err);
        }

        try {
            $("#flexiselDemo4").flexisel({
                clone: false
            });
        } catch(err) {
            console.log("Error in flexiselDemo4: " + err);
        }

    });
</script>
