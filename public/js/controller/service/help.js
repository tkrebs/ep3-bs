(function() {

    $(document).ready(function() {

        var helpPanel = $("#help-panel");

        if (helpPanel.length) {
            helpPanel.find("img").closest("a").css("opacity", 1.0).wrap('<div class="panel">');
        }

    });

})();