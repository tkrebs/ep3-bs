(function() {

    var adminboxLink;

    var adminbox;
    var adminboxShutdown = false;

    var animationDuration = 350;

    $(document).ready(function() {

        adminboxLink = $("#admin-menu-link");

        adminboxLink.on("click", function(event) {
            event.preventDefault();

            if (! adminbox) {
                event.stopPropagation();

                loadAdminbox( adminboxLink.attr("href") );
            } else {
                removeAdminbox();
            }
        });

        $(window).resize(updateAdminbox);

        $("body").on("click", function(event) {
            if (adminbox) {
                var target = $(event.target);

                if (target[0] !== adminbox[0] && target.closest(".panel")[0] !== adminbox[0]) {
                    removeAdminbox();
                }
            }
        });

    });

    function loadAdminbox(href)
    {
        var calendarSquareboxTemplate = $("#calendar-squarebox-template");

        if (calendarSquareboxTemplate.length) {
            populateAdminbox( calendarSquareboxTemplate.html() );
        }

        adminbox.queue(function() {
            $.ajax({
                "cache": false,
                "data": { "ajax": true },
                "dataType": "html",
                "error": function() {
                    if (adminbox && ! adminboxShutdown) {
                        window.location.href = href;
                    }
                },
                "success": function (data) {
                    if (adminbox && ! adminboxShutdown) {
                        populateAdminbox(data);

                        adminbox.find(".no-ajax").remove();

                        updateAdminbox();
                    }
                },
                "url": href
            });

            $(this).dequeue();
        });
    }

    function prepareAdminbox()
    {
        if (! adminbox) {
            adminbox = $('<div class="panel"></div>').css({
                "position": "absolute",
                "z-index": 1536
            });

            $("body").prepend(adminbox);
        }
    }

    function populateAdminbox(content)
    {
        prepareAdminbox();

        adminbox.clearQueue();
        adminbox.css("opacity", 0.01);
        adminbox.html(content);

        updateAdminbox();

        adminbox.fadeTo(animationDuration, 1.00);
    }

    function updateAdminbox()
    {
        if (adminbox) {
            adminbox.position({
                "my": "center top+8",
                "at": "center bottom",
                "of": adminboxLink
            });
        }
    }

    function removeAdminbox()
    {
        if (adminbox) {
            adminboxShutdown = true;

            adminbox.clearQueue().fadeOut(animationDuration, function() {
                adminbox.remove();
                adminbox = undefined;
                adminboxShutdown = false;
            });
        }
    }

})();