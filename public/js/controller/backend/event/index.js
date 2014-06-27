(function() {

    $(document).ready(function() {

        /* Show actions on row hover */

        if ($(".actions-col").length > 1) {
            $(".actions-col").css("opacity", 0.5);

            $("tr").hover(function() {
                $(this).find(".actions-col").fadeTo(100, 1.0);
            }, function() {
                $(this).find(".actions-col").fadeTo(100, 0.5);
            });
        }

    });

})();