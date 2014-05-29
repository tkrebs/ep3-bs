(function() {

    $(document).ready(function() {

        /* Remove notes col if no notes */

        $(".notes-col").hide();

        $("td.notes-col").each(function() {
            if ($(this).text() !== '-') {
                $(".notes-col").show();
            }
        });

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