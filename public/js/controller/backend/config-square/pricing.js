(function() {

    $(document).ready(function() {

        $("#pricing-table").on("click", ".pricing-date-range-new", function(event) {
            event.preventDefault();

            var template = $("#pricing-table-template").html();

            $(this).closest("table").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            $(this).closest("table").find("tr:last").siblings("tr:last").hide().fadeIn();

            $(".datepicker").datepicker();
        });

        $("#pricing-table").on("click", ".pricing-day-range-new", function(event) {
            event.preventDefault();

            var template = $("#pricing-table-template").find(".pricing-day-range").closest("table").closest("td").html();

            $(this).closest("table").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            $(this).closest("table").find("tr:last").siblings("tr:last").hide().fadeIn();
        });

        $("#pricing-table").on("click", ".pricing-time-range-new", function(event) {
            event.preventDefault();

            var template = $("#pricing-table-template").find(".pricing-time-range").closest("tr").closest("td").html();

            $(this).closest("table").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            $(this).closest("table").find("tr:last").siblings("tr:last").hide().fadeIn();
        });

        $("#pricing-table").on("click", ".pricing-price-new", function(event) {
            event.preventDefault();

            var template = $("#pricing-table-template").find(".pricing-price").closest("tr").html();

            $(this).closest("table").find("tr:last").before('<tr>' + template + '</tr>');
            $(this).closest("table").find("tr:last").siblings("tr:last").hide().fadeIn();

            $(".tooltip").tooltip();
        });

        $("#pricing-table").on("click", ".pricing-delete", function(event) {
            event.preventDefault();

            var fadeTime = 200;

            if ($(this).closest("tbody").children("tr").length > 2) {
                $(this).closest("tr").fadeOut(fadeTime, function() { $(this).remove(); });
            } else {
                if ($(this).parents("tbody:eq(2)").children("tr").length > 2) {
                    $(this).parents("tr:eq(2)").fadeOut(fadeTime, function() { $(this).remove(); });
                } else {
                    if ($(this).parents("tbody:eq(4)").children("tr").length > 2) {
                        $(this).parents("tr:eq(4)").fadeOut(fadeTime, function() { $(this).remove(); });
                    } else {
                        $(this).parents("tr:eq(6)").fadeOut(fadeTime, function() { $(this).remove(); });
                    }
                }
            }
        });

        $("#pricing-save").on("click", function(event) {
            $("#pricing-form-rules input").remove();

            var i = 0;

            $("#pricing-table .pricing-price").each(function(index, element) {
                var pricing = $(element);
                var timeRange = pricing.parents("tbody:eq(1)").find(".pricing-time-range");
                var dayRange = timeRange.parents("tbody:eq(2)").find(".pricing-day-range");
                var dateRange = dayRange.parents("tbody:eq(2)").find(".pricing-date-range");

                var dateStart = dateRange.find("input.datepicker:first").val();
                var dateEnd = dateRange.find("input.datepicker:last").val();
                var dayStart = dayRange.find("select:first").val();
                var dayEnd = dayRange.find("select:last").val();
                var timeStart = timeRange.find("input.timepicker:first").val();
                var timeEnd = timeRange.find("input.timepicker:last").val();
                var price = pricing.find("input.pricepicker").val();
                var gross = pricing.find("select.pricing-rate-gross").val();
                var rate = pricing.find("input.pricing-rate").val();

                var priority = index;

                var sid = pricing.find("select:last").val();

                var timeBlock = pricing.find("input.timeblockpicker").val();

                // Check date
                if (! dateStart.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}$/)) {
                    window.alert("Hinweis: Das Datum \"" + dateStart + "\" ist ungültig - Format: 24.12.2014");

                    event.preventDefault();
                    return;
                }

                if (! dateEnd.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}$/)) {
                    window.alert("Hinweis: Das Datum \"" + dateEnd + "\" ist ungültig - Format: 24.12.2014");

                    event.preventDefault();
                    return;
                }

                if (timeStart.match(/^[0-9]{0,1}[0-9]$/)) {
                    timeStart += ":00";
                }

                if (! timeStart.match(/^[0-9]{0,1}[0-9]:[0-9][0-9]$/)) {
                    window.alert("Hinweis: Die Uhrzeit \"" + timeStart + "\" ist ungültig - Format: 23:15");

                    event.preventDefault();
                    return;
                }

                if (timeEnd.match(/^[0-9]{0,1}[0-9]$/)) {
                    timeEnd += ":00";
                }

                if (! timeEnd.match(/^[0-9]{0,1}[0-9]:[0-9][0-9]$/)) {
                    window.alert("Hinweis: Die Uhrzeit \"" + timeEnd + "\" ist ungültig - Format: 23:15");

                    event.preventDefault();
                    return;
                }

                if (price.match(/^[0-9]+$/)) {
                    price += ",00";
                }

                if (! price.match(/^[0-9]+,[0-9][0-9]$/)) {
                    window.alert("Hinweis: Der Preis \"" + price + "\" ist ungültig - Format: 19,95");

                    event.preventDefault();
                    return;
                }

                if (! timeBlock.match(/^[1-9][0-9]*$/)) {
                    window.alert("Hinweis: Die Minutenangabe \"" + timeBlock + "\" ist ungültig");

                    event.preventDefault();
                    return;
                }

                var data = JSON.stringify( [sid, priority, dateStart, dateEnd, dayStart, dayEnd, timeStart, timeEnd, price, rate, gross, timeBlock] );

                $("#pricing-form-rules").append('<input type="hidden" name="pricing-rule-' + index + '" value="' + encodeURI(data) + '">');

                i++;
            });

            $("#pricing-rules-count").val(i);
        });

        /* Reconstruct ruleset */

        var pricingRules = window.pricingRules; // Quick and dirty, I know :O

        var latestStartEndDate;
        var latestStartEndDay;
        var latestStartEndTime;

        $.each(pricingRules, function(index, element) {
            var sid = element[1];
            var dateStart = element[3];
            var dateEnd = element[4];
            var dayStart = element[5];
            var dayEnd = element[6];
            var timeStart = element[7];
            var timeEnd = element[8];
            var price = element[9];
            var rate = element[10];
            var gross = element[11];
            var timeBlock = element[12];

            if (! sid) {
                sid = "null";
            }

            var thisStartEndDate = "" + dateStart + dateEnd;
            var thisStartEndDay = "" + dayStart + dayEnd;
            var thisStartEndTime = "" + timeStart + timeEnd;

            if (thisStartEndDate !== latestStartEndDate) {
                var template = $("#pricing-table-template").html();

                $("#pricing-table").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            } else if (thisStartEndDay !== latestStartEndDay) {
                var template = $("#pricing-table-template").find(".pricing-day-range").closest("table").closest("td").html();

                $("#pricing-table").find(".pricing-day-range:last").parents("tbody:eq(1)").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            } else if (thisStartEndTime !== latestStartEndTime) {
                var template = $("#pricing-table-template").find(".pricing-time-range").closest("table").closest("td").html();

                $("#pricing-table").find(".pricing-time-range:last").parents("tbody:eq(1)").find("tr:last").before('<tr><td>' + template + '</td></tr>');
            } else {
                var template = $("#pricing-table-template").find(".pricing-price").html();

                $("#pricing-table").find(".pricing-price:last").parents("tbody:eq(1)").find("tr:last").before('<tr><td class="pricing-price">' + template + '</td></tr>');
            }

            if (price >= 100) {
                price = price.substring(0, price.length - 2) + "," + price.substring(price.length - 2);
            } else if (price >= 10) {
                price = "0," + price;
            } else {
                price = "0,0" + price;
            }

            $("#pricing-table .pricing-dateStart:last").val(dateStart);
            $("#pricing-table .pricing-dateEnd:last").val(dateEnd);
            $("#pricing-table .pricing-dayStart:last").val(dayStart);
            $("#pricing-table .pricing-dayEnd:last").val(dayEnd);
            $("#pricing-table .pricing-timeStart:last").val(timeStart.substring(0, 5));
            $("#pricing-table .pricing-timeEnd:last").val(timeEnd.substring(0, 5));
            $("#pricing-table .pricing-price-number:last").val(price);
            $("#pricing-table .pricing-rate-gross:last").val(gross);
            $("#pricing-table .pricing-rate:last").val(rate);
            $("#pricing-table .pricing-sid:last").val(sid);
            $("#pricing-table .pricing-timeBlock:last").val(Math.round(timeBlock / 60));

            latestStartEndDate = thisStartEndDate;
            latestStartEndDay = thisStartEndDay;
            latestStartEndTime = thisStartEndTime;
        });

        $(".datepicker").datepicker();
    });

})();
