(function() {

    $(document).ready(function() {

        /* Autofocus */

        $(".autofocus").focus();

        /* Messages */

        $(".message").each(prepareMessage);

        $(document).on("click", ".dismiss-message-link", dismissMessage);

        /* Inline labels */

        $(".inline-label").find("span").show();

        $(".inline-label-container").each(function() {
            updateInlineLabel( $(this) );
        });

        $(document).on("change focus focusin focusout blur keydown", ".inline-label-container", function() {
            updateInlineLabel( $(this) );
        });

        /* Datepickers */

        $(document).ready(prepareDatepicker);

        $(".datepicker").datepicker();

        /* Tooltips */

        $(document).tooltip({
            "content": function() {
                return $(this).data("tooltip");
            },
            "items": "[data-tooltip]",
            "position": { "my": "center top+8", "at": "center bottom", "collision": "flipfit", "within": "#content" }
        });

        /* Links panel */

        $(window).resize(updateLinksPanel);
        $(document).ready(updateLinksPanel);
        $(document).on("updateLayout", updateLinksPanel);

        /* Popup links */

        $(document).on("click", "a.popup-link", openPopup);

    });

    function updateLinksPanel()
    {
        var linksPanel = $(".links").first();
        var targetPanel = $("#content > .centered-panel.content-panel").first();

        if (! targetPanel.length) {
            targetPanel = $("#content > .centered-panel").first();
        }

        if (linksPanel.length && targetPanel.length) {
            var targetPanelWidth = targetPanel.outerWidth();
            var targetPanelMarginTop = parseInt(targetPanel.css("margin-top"));
            var targetPanelMarginLeft = parseInt(targetPanel.css("margin-left"));

            if (isNaN(targetPanelMarginTop)) {
                targetPanelMarginTop = 0;
            }

            if (isNaN(targetPanelMarginLeft)) {
                targetPanelMarginLeft = 0;
            }

            /* Determine back links */

            var linksBack = linksPanel.find(".links-back").first();
            var linksBackWidth = 0;

            if (linksBack.length) {
                linksBack.css("position", "absolute");
                linksBackWidth = linksBack.outerWidth(true);
            }

            /* Determine forth links */

            var linksForth = linksPanel.find(".links-forth").first();
            var linksForthWidth = 0;

            if (linksForth.length) {
                linksForth.css("position", "absolute");
                linksForthWidth = linksForth.outerWidth(true);
            }

            /* Determine overall reference width */

            var referenceWidth = targetPanelWidth + Math.max(linksBackWidth, linksForthWidth) * 2;

            /* Determine links panel display mode */

            if (referenceWidth >= $(window).width()) {
                linksBack.removeAttr("style");
                linksForth.removeAttr("style");
            } else {
                var targetPanelLeft;

                if (targetPanelMarginLeft > 0) {
                    var targetParentPaddingLeft = parseInt(targetPanel.parent().css("padding-left"));

                    if (isNaN(targetParentPaddingLeft)) {
                        targetParentPaddingLeft = 0;
                    }

                    targetPanelLeft = targetPanelMarginLeft + targetParentPaddingLeft;
                } else {
                    targetPanelLeft = Math.floor(targetPanel.position().left);
                }

                linksBack.css({
                    "left": targetPanelLeft - linksBackWidth,
                    "top": Math.min(targetPanel.position().top + targetPanelMarginTop + Math.round(targetPanel.outerHeight() / 2) - Math.round(linksBack.outerHeight() / 2), 384)
                });

                linksForth.css({
                    "left": targetPanelLeft + targetPanelWidth,
                    "top": Math.min(targetPanel.position().top + targetPanelMarginTop + Math.round(targetPanel.outerHeight() / 2) - Math.round(linksForth.outerHeight() / 2), 384)
                });
            }
        }
    }

    function prepareMessage(index)
    {
        var that = $(this);

        blink(that, index * 100);

        if (that.is(".default-message, .success-message, .info-message, .error-message")) {
            if (that.closest(".messages-panel").siblings(".centered-panel").length) {
                that.prepend('<a href="#" class="unlined white dismiss-message-link" style="float:right;">&times;</a>');
            }
        }
    }

    function dismissMessage(event)
    {
        event.preventDefault();

        var messagesPanel = $(this).closest(".messages-panel");
        var messages = messagesPanel.find(".message");

        if (messages.length) {
            var message = $(this).closest(".message");

            message.fadeOut(500, function() {
                message.remove();

                $(document).trigger("updateLayout");
            });
        } else {
            messagesPanel.fadeOut(500, function() {
                messagesPanel.remove();

                $(document).trigger("updateLayout");
            });
        }
    }

    function prepareDatepicker()
    {
        var locale = $("html").attr("lang");
        var basePath = $("#logo").attr("href");

        if (locale && locale !== "en-US") {
            $("body").append('<script type="text/javascript" src="' + basePath + 'js/jquery-ui/i18n/' + locale + '.js"></script>');
        }

        $.datepicker.setDefaults({
            "altFormat": "M d, yy",
            "dateFormat": "M d, yy",
            "onSelect": function() {
                that = $(this);

                that.trigger("change");

                updateInlineLabel(that);

                if (that.is(".datepicker-autosubmit")) {
                    that.closest("form").submit();
                }
            },
            "showAnim": "slideDown"
        });
    }

    function openPopup(event)
    {
        var link = $(this);

        var popup = window.open(link.attr("href"), "bs-popup", "dependent=yes,height=512,left=64,location=no,menubar=no,resizable=yes,top=64,width=1024");

        if (! (! popup || popup.closed || typeof popup.closed=='undefined')) {
            event.preventDefault();
        }
    }

})();

function updateInlineLabel(input)
{
    var label = input.siblings(".inline-label");

    if (label.length) {
        if (input.val()) {
            label.find("span").clearQueue().hide();
        } else if (input.is(":focus")) {
            label.find("span").clearQueue().delay(100).fadeOut(300);
        } else {
            label.find("span").clearQueue().delay(100).fadeIn(300);
        }
    }
}

function blink(element, delay, length, strength)
{
    if (! element) {
        return;
    }

    if (! delay) {
        delay = 0;
    }

    if (! length) {
        length = 300;
    }

    if (! strength) {
        strength = 0.25;
    }

    element.delay(delay).fadeTo(length, strength, function() {
        element.fadeTo(length, 1.0);
    });
}