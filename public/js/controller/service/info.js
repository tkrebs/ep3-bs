(function() {

    $(document).ready(function() {

        var infoPanel = $("#info-panel");

        if (infoPanel.length) {
            infoPanel.find("img").closest("a").css("opacity", 1.0).wrap('<div class="panel">');
        }

    });

})();