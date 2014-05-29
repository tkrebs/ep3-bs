(function() {

    var fadingDuration = 0;

    $(document).ready(function() {

        updateForm();

        fadingDuration = "normal";

        $("#cf-maintenance").change(updateForm);
        $("#cf-registration").change(updateForm);

    });

    function updateForm()
    {
        /* Maintenance */

        var value = $("#cf-maintenance").val();

        if (value === "true") {
            $("#cf-maintenance-message").closest("tr").fadeIn(fadingDuration);
        } else {
            $("#cf-maintenance-message").closest("tr").fadeOut(fadingDuration);
        }

        /* Registration */

        value = $("#cf-registration").val();

        if (value === "false") {
            $("#cf-registration-message").closest("tr").fadeIn(fadingDuration);
        } else {
            $("#cf-registration-message").closest("tr").fadeOut(fadingDuration);
        }
    }

})();