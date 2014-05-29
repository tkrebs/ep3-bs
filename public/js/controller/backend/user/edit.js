(function() {

    $(document).ready(function() {
        var firstnameLabel = $('label[for="euf-firstname"]');
        var firstnameLabelOriginal = firstnameLabel.text();

        var firstnameInput = $("#euf-firstname");
        var lastnameInput = $("#euf-lastname");
        var firstnameInputOriginal = firstnameInput.width();

        var phoneInput = $("#euf-phone");
        var birthdateInput = $("#euf-birthdate");

        var genericLabel = $("#euf-generic-label").text();

        function updateEditForm()
        {
            var uid = $("#euf-uid");

            if (! uid.val()) {
                uid.closest("tr").hide();
            }

            /* Update status and gender fields */

            var status = $("#euf-status");
            var gender = $("#euf-gender");

            if (gender.val() === "family" || gender.val() === "firm") {
                firstnameInput.css("width", phoneInput.css("width"));
                lastnameInput.hide();
                birthdateInput.parents("tr").hide();
                firstnameLabel.html(genericLabel);
            } else {
                firstnameInput.css("width", firstnameInputOriginal);
                lastnameInput.show();
                birthdateInput.parents("tr").show();
                firstnameLabel.html(firstnameLabelOriginal);
            }

            /* Update privileges */

            if (status.val() === "assist") {
                $("#euf-privileges").closest("tr").show();
            } else {
                $("#euf-privileges").closest("tr").hide();
            }
        }

        $("#euf-status").change(updateEditForm);
        $("#euf-gender").change(updateEditForm);
        updateEditForm();
    });

})();