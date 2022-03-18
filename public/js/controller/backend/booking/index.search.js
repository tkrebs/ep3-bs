(function() {

    $(document).ready(function() {

        /* Filters */

        var searchInput = $("#bs-filter");

        $("#bs-filters-help").on("click", function(event) {
            event.preventDefault();

            var filtersBox = $("#bs-filters-help-box");

            if (filtersBox.length) {
                filtersBox.width($(this).closest("table").width());

                if (filtersBox.is(":visible")) {
                    filtersBox.slideUp();
                } else {
                    filtersBox.slideDown();
                }
            }
        });

        $(".bs-filter-snippet").on("click", function(event) {
            event.preventDefault();

            var snippet = $(this).find("code").text();

            searchInput.val(searchInput.val() + " " + snippet);
        });

    });

})();
