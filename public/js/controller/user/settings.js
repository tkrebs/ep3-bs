(function() {

    $(document).ready(function() {

        var editPanels = $(".edit-panel");
        editPanels.hide();

        var editLabels = $(".edit-label");
        editLabels.css("cursor", "pointer");

        editLabels.hover(function() {
            $(this).css("color", "#333");
        }, function() {
            $(this).css("color", "");
        });

        editLabels.click(function() {
            var that = $(this);

            if (that.siblings(".edit-panel").is(":hidden")) {
                that.closest(".sandbox").siblings(".sandbox").find(".edit-panel:visible").slideUp();
                that.siblings(".edit-panel").slideDown();
            } else {
                that.siblings(".edit-panel").slideUp();
            }
        });

        /* Sandboxes with error messages should be visible */

        editPanels.each(function() {
            var that = $(this);

            if (that.find(".message").length) {
                that.show();
            }
        });

    });

})();