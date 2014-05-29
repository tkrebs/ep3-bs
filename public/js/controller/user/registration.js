(function() {

    $(document).ready(function() {
        var firstnameLabel = $('label[for="rf-firstname"]');
        var firstnameLabelOriginal = firstnameLabel.text();

        var firstnameInput = $("#rf-firstname");
        var lastnameInput = $("#rf-lastname");
        var firstnameInputOriginal = firstnameInput.width();

        var phoneInput = $("#rf-phone");
        var birthdateInput = $("#rf-birthdate");

        var genericLabel = $("#rf-generic-label").text();

        function updateRegistrationForm()
        {
            var genderSelect = $("#rf-gender");

            if (genderSelect.val() === "family" || genderSelect.val() === "firm") {
                firstnameInput.css("width", phoneInput.css("width"));
                lastnameInput.hide();
                birthdateInput.closest("tr").hide();
                firstnameLabel.html(genericLabel);
            } else {
                firstnameInput.css("width", firstnameInputOriginal);
                lastnameInput.show();
                birthdateInput.closest("tr").show();
                firstnameLabel.html(firstnameLabelOriginal);
            }
        }

        $(document).ready(updateRegistrationForm);
        $("#rf-gender").change(updateRegistrationForm);

        /* Security related */

        if ($("#form-nickname-error").length) {
            $('input[name="rf-nickname"]').show();
        }
    });

})();