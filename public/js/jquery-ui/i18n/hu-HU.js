jQuery(function($) {
	$.datepicker.regional["hu"] = {
        "altFormat": "dd.mm.yy",
        "closeText": "Bezár",
        "currentText": "Ma",
        "dateFormat": "dd.mm.yy",
        "dayNames": ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"],
        "dayNamesMin": ["Va", "Hé", "Ke", "Sze", "Cs", "Pé", "Szo"],
        "dayNamesShort": ["Va", "Hé", "Ke", "Sze", "Cs", "Pé", "Szo"],
        "firstDay": 1,
        "monthNames": ["Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December"],
        "monthNamesShort": ["Jan", "Feb", "Már", "Ápr", "Máj", "Jún", "Júl", "Aug", "Szep", "Okt", "Nov", "Dec"],
        "nextText": "Következő",
        "prevText": "Előző"
    };

	$.datepicker.setDefaults($.datepicker.regional["hu"]);
});
