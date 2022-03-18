(function() {

    $(document).ready(function() {

        updateFormReadonlyMessage(true);

        $("#cf-status").on("change focusout", updateFormReadonlyMessage);

        updateFormCapacityAskNames(true);

        $("#cf-capacity").on("change keyup focusout", updateFormCapacityAskNames);
    });

    function updateFormReadonlyMessage(instant)
    {
        var value = $("#cf-status").val();

        if (value === "readonly") {
            $("#cf-readonly-message").closest("tr").fadeIn(instant === true ? 0 : "normal");
        } else {
            $("#cf-readonly-message").closest("tr").fadeOut(instant === true ? 0 : "normal");
        }
    }

    function updateFormCapacityAskNames(instant)
    {
        var capacity = $("#cf-capacity").val();

        if (parseInt(capacity) > 1) {
            $("#cf-capacity-ask-names").closest("tr").fadeIn(instant === true ? 0 : "normal");
        } else {
            $("#cf-capacity-ask-names").closest("tr").fadeOut(instant === true ? 0 : "normal");
        }
    }

})();
