<script>
    $(document).ready(function () {
        $('#filter').keyup(function () {
            delay(function () {
                $("#loader").fadeIn(200);
                $.ajax({
                    method: "post",
                    url: "<?php echo site_url('filter/filter_ajax/' . $filter_method); ?>",
                    data: {
                        filter_query: $('#filter').val()
                    }
                }).done(function (data) {
                    $("#loader").fadeOut(200);
                    $('.filter-results').html(data);
                });
            }, 750);
        });
    });
</script>