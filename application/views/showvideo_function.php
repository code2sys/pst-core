<script type="application/javascript">
    function showVideo(vidId, vidTit, override_id, override_vdottle) {
        if (!override_id) {
            override_id = "mainVideo";
        }

        if (!override_vdottle) {
            override_vdottle = "vdottl";
        }

        var mainVideo = $('#' + override_id).data('id');
        //var mainTitle = $('.vdottl').html();
        $('.' + override_vdottle).html(vidTit);
        $("#" + override_id)[0].src = "https://www.youtube.com/embed/" + vidId + "?rel=0&autoplay=1";
        $('#' + override_id).data('id', vidId);
        //$('.shwVidHalf').show();
        $('#' + vidId).hide();
        $('#' + mainVideo).show();
        //$("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
    }
</script>