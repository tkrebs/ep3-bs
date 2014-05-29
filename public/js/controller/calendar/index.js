(function() {

    var calendar;

    var squarebox;
    var squareboxShutdown = false;

    var squareboxOverlay;

    var loadingDelay = 300;
    var animationDuration = 350;

    $(document).ready(function() {

        calendar = $(".calendar-table");

        /* Squarebox */

        calendar.on("click", "a.calendar-cell", function(event) {
            event.preventDefault();

            if (! squarebox) {
                event.stopPropagation();

                loadSquarebox( $(this).attr("href") );
            }
        });

        $(window).resize(updateSquarebox);

        $("body").on("click", "#squarebox-overlay", function() {
            removeSquarebox();
        });

        /* Group highlighting */

        $("a.calendar-cell").hover(function() {
            var that = $(this);
            var classes = that.attr("class");
            var group = classes.match(/cc-group-\d+/);

            $("a." + group).css({ "opacity": 0.9, "background-color": that.css("background-color") });
            that.css("opacity", 1.0);
        }, function() {
            var classes = $(this).attr("class");
            var group = classes.match(/cc-group-\d+/);

            $("a." + group).removeAttr("style");
        });

        /* Update calendar */

        updateCalendarCols();
        $(window).resize(updateCalendarCols);

        /* Beautify calendar */

        if ($("body").height() < 576) {
            $(".content-panel").css("padding", 30);
        }

    });

    function loadSquarebox(href)
    {
        var calendarSquareboxTemplate = $("#calendar-squarebox-template");

        if (calendarSquareboxTemplate.length) {
            populateSquarebox( calendarSquareboxTemplate.html() );
        } else {
            populateSquarebox('<div class="padded">...</p>');
        }

        squarebox.clearQueue().delay(loadingDelay).queue(function() {
            $.ajax({
                "cache": false,
                "data": { "ajax": true },
                "dataType": "html",
                "error": function() {
                    if (squarebox && ! squareboxShutdown) {
                        window.location.href = href;
                    }
                },
                "success": function (data) {
                    if (squarebox && ! squareboxShutdown) {
                        populateSquarebox(data);

                        squarebox.find(".no-ajax").remove();
                        squarebox.find(".datepicker").datepicker();

                        squarebox.find(".inline-label-container").each(function() {
                            updateInlineLabel( $(this) );
                        });

                        updateSquarebox();

                        /* Recognize squarebox internal links */

                        squarebox.on("click", "a.squarebox-internal-link", function(event) {
                            event.preventDefault();

                            loadSquarebox( $(this).attr("href") );
                        });

                        /* Recognize squarebox close links */

                        squarebox.on("click", "a.squarebox-close-link", function(event) {
                            event.preventDefault();

                            removeSquarebox();
                        });
                    }
                },
                "url": href
            });

            $(this).dequeue();
        });
    }

    function prepareSquarebox()
    {
        if (! squareboxOverlay) {
            squareboxOverlay = $('<div id="squarebox-overlay"></div>').css({
                "position": "absolute",
                "z-index": 1532,
                "opacity": 0.00,
                "width": $(document).width(), "height": $(document).height(),
                "left": 0, "top": 0,
                "background": "#333"
            });

            $("body").prepend(squareboxOverlay);
        }

        if (! squarebox) {
            squarebox = $('<div class="panel"></div>').css({
                "position": "absolute",
                "z-index": 1536
            });

            $("body").prepend(squarebox);
        }
    }

    function populateSquarebox(content)
    {
        prepareSquarebox();

        squarebox.clearQueue();
        squarebox.css("opacity", 0.01);
        squarebox.html(content);

        updateSquarebox();

        squarebox.fadeTo(animationDuration, 1.00);

        fadeOutContent();
    }

    function updateSquarebox()
    {
        if (squarebox) {
            var orientation;

            if ($("body").height() > $(window).height()) {
                orientation = window;
            } else {
                orientation = calendar;
            }

            squarebox.position({
                "my": "center",
                "at": "center",
                "of": orientation
            });
        }
    }

    function removeSquarebox()
    {
        if (squarebox) {
            squareboxShutdown = true;

            squarebox.clearQueue().fadeOut(animationDuration, function() {
                if (squarebox) {
                    squarebox.remove();
                    squarebox = undefined;
                }

                squareboxShutdown = false;
            });

            fadeInContent();
        }
    }

    function fadeOutContent()
    {
        if (squareboxOverlay) {
            squareboxOverlay.clearQueue().fadeTo(animationDuration, 0.75);
        }
    }

    function fadeInContent()
    {
        if (squareboxOverlay) {
            squareboxOverlay.clearQueue().fadeTo(animationDuration, 0.00, function() {
                if (squareboxOverlay) {
                    squareboxOverlay.remove();
                    squareboxOverlay = undefined;
                }
            });
        }
    }

    function updateCalendarCols()
    {
        var calendarWidth = $("#calendar").width();
        var calendarLegendColWidth = $(".calendar-time-col, .calendar-square-col").width();

        var calendarDateCols = $(".calendar-date-col:visible");

        if (calendarWidth && calendarLegendColWidth && calendarDateCols.length) {
            calendarDateCols.width( Math.floor((calendarWidth - calendarLegendColWidth) / calendarDateCols.length) );
        }
    }

})();