jQuery(function($) {
	$.datepicker.regional["de"] = {
        "altFormat": "dd.mm.yy",
        "closeText": "Schließen",
        "currentText": "Heute",
        "dateFormat": "dd.mm.yy",
        "dayNames": ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
        "dayNamesMin": ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        "dayNamesShort": ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        "firstDay": 1,
        "monthNames": ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        "monthNamesShort": ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        "nextText": "Vor",
        "prevText": "Zurück"
    };

	$.datepicker.setDefaults($.datepicker.regional["de"]);
});