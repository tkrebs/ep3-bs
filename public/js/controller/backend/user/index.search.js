(function() {

    $(document).ready(function() {

        /* Autocomplete */

        var searchInput = $("#usf-search");

        searchInput.autocomplete({
            "minLength": 1,
            "source": searchInput.data("autocomplete-url")
        });

        /* Filters */

        $("#usf-filters-link").on("click", function(event) {
            event.preventDefault();

            var filtersBox = $("#usf-filters-box");

            if (filtersBox.length) {
                filtersBox.width($(this).closest("table").width());

                if (filtersBox.is(":visible")) {
                    filtersBox.slideUp();
                } else {
                    filtersBox.slideDown();
                }
            }
        });

        $(".usf-filter-snippet").on("click", function(event) {
            event.preventDefault();

            var snippet = $(this).find("code").text();

            searchInput.val(searchInput.val() + " " + snippet);
        });

    });

})();