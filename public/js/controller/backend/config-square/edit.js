(function() {

    var fadingDuration = 0;

    $(document).ready(function() {

        updateForm();

        fadingDuration = "normal";

        $("#cf-status").change(updateForm);

    });

    function updateForm()
    {
        var value = $("#cf-status").val();

        if (value === "readonly") {
            $("#cf-readonly-message").closest("tr").fadeIn(fadingDuration);
        } else {
            $("#cf-readonly-message").closest("tr").fadeOut(fadingDuration);
        }
    }

})();