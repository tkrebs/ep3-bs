(function() {

    $(document).ready(function() {

        var sbButton = $("#sb-button");

        $("#sb-customization-panel-warning").remove();
        $("#sb-customization-panel").show();

        $("#sb-quantity").on("change", function() {
            if (sbButton.length) {
                var quantity = $(this).val();
                var oldHref = sbButton.attr("href");
                var newHref = oldHref.replace(/q=[0-9]+/, "q=" + quantity);

                sbButton.attr("href", newHref);
            }
        });

        $(".sb-product").on("change", function() {
            if (sbButton.length) {

                var products = "";

                $(".sb-product").each(function(index, element) {
                    var spid = $(element).data("spid");
                    var value = $(element).val();

                    if (value > 0) {
                        products += spid + ":" + value + ",";
                    }
                });

                if (products) {
                    products = products.substr(0, products.length - 1);
                } else {
                    products = "0";
                }

                var oldHref = sbButton.attr("href");
                var newHref = oldHref.replace(/p=[0-9\:\,]+/, "p=" + products);

                sbButton.attr("href", newHref);
            }
        });

    });

})();