jQuery(function($) {
	$.datepicker.regional["fr"] = {
        "altFormat": "dd.mm.yy",
        "closeText": "Fermer",
        "currentText": "Aujourd'hui",
        "dateFormat": "dd.mm.yy",
        "dayNames": [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
        "dayNamesMin": [ "D","L","M","M","J","V","S" ],
        "dayNamesShort": [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
        "firstDay": 1,
        "monthNames": [ "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
        "monthNamesShort": [ "janv.", "févr.", "mars", "avr.", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc." ],
        "nextText": "Suivant",
        "prevText": "Précédent"
    };

	$.datepicker.setDefaults($.datepicker.regional["fr"]);
});