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
            var that = $(this);

            if (! that.hasClass("squarebox-external-link")) {
                event.preventDefault();

                if (!squarebox) {
                    event.stopPropagation();

                    loadSquarebox(that.attr("href"));
                }
            }
        });

        $(window).resize(updateSquarebox);

        $(window).on("squarebox.update", updateSquarebox);

        $("body").on("click", "#squarebox-overlay", function() {
            removeSquarebox();
        });

        /* Group highlighting */

        $("a.calendar-cell").hover(function() {
            var that = $(this);
            var classes = that.attr("class");
            var group = classes.match(/cc-group-\d+/);

            if (group) {
                var groupMembers = $("a." + group);

                groupMembers.each(function() {
                    $(this).data("original-style", $(this).attr("style"));
                });

                groupMembers.css({"opacity": 0.9, "background-color": that.css("background-color")});

                that.css("opacity", 1.0);
            }
        }, function() {
            var that = $(this);
            var classes = that.attr("class");
            var group = classes.match(/cc-group-\d+/);

            if (group) {
                var groupMembers = $("a." + group);

                groupMembers.each(function() {
                    $(this).attr("style", $(this).data("original-style"));
                });
            }
        });

        /* Update calendar */

        updateCalendarCols();
        $(window).resize(updateCalendarCols);
        $(document).on("updateLayout", updateCalendarCols);

        /* Update calendar events */

        updateCalendarEvents();
        $(window).resize(updateCalendarEvents);
        $(document).on("updateLayout", updateCalendarEvents);

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

                        squarebox.append('<a href="#" class="squarebox-primary-close-link squarebox-close-link">&times;</a>');

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

    function updateCalendarEvents()
    {
        $(".calendar-date-col").each(function(dateIndex) {
            var calendarDateCol = $(this);

            var eventGroups = [];

            calendarDateCol.find(".cc-event").each(function() {
                var classes = $(this).attr("class");
                var eventGroup = classes.match(/cc-group-\d+/);

                if (eventGroup) {
                    if ($.inArray(eventGroup, eventGroups) === -1) {
                        eventGroups.push(eventGroup);
                    }
                }
            });

            var eventGroupsLength = eventGroups.length;

            for (var i = 0; i <= eventGroupsLength; i++) {
                var eventGroup = eventGroups[i] + "";

                var eventGroupCellFirst = calendarDateCol.find("." + eventGroup + ":first");
                var eventGroupCellLast = calendarDateCol.find("." + eventGroup + ":last");

                var posFirst = eventGroupCellFirst.position();
                var posLast = eventGroupCellLast.position();

                if (posFirst && posLast) {
                    var startX = Math.floor(posFirst.left) - 1;
                    var startY = Math.floor(posFirst.top) - 1;

                    var endX = Math.ceil(posLast.left) + 1;
                    var endY = Math.ceil(posLast.top) + 1;

                    var eventWidth = Math.round((endX + eventGroupCellLast.outerWidth()) - startX);
                    var eventHeight = Math.round((endY + eventGroupCellLast.outerHeight()) - startY);

                    /* Create event group overlay */

                    var eventGroupOverlay = $("#" + eventGroup + "-overlay-" + dateIndex);

                    if (! eventGroupOverlay.length) {
                        eventGroupOverlay = eventGroupCellFirst.clone();
                        eventGroupOverlay.appendTo( eventGroupCellFirst.closest("td") );
                        eventGroupOverlay.attr("id", eventGroup + "-overlay-" + dateIndex);
                        eventGroupOverlay.removeClass(eventGroup);
                    }

                    var eventGroupOverlayLabel = eventGroupOverlay.find(".cc-label");

                    eventGroupOverlay.css({
                        "position": "absolute",
                        "z-index": 128,
                        "left": startX, "top": startY,
                        "width": eventWidth,
                        "height": eventHeight,
                        "padding": 0
                    });

                    eventGroupOverlayLabel.css({
                        "height": "auto",
                        "font-size": "12px",
                        "line-height": 1.5
                    });

                    eventGroupOverlayLabel.css({
                        "position": "relative",
                        "top": Math.round((eventHeight / 2) - (eventGroupOverlayLabel.height() / 2))
                    });
                }
            }
        });
    }

})();
