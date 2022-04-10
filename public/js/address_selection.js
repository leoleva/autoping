/*
$( document ).ready(function() {

    jQuery("#country").change(function () {
        const countryId = this.value

        if (countryId === '') {
            return;
        }

        $.get("/countries/" + countryId + "/cities", function( data ) {
            var $el = jQuery("#city");
            $el.empty();
            $el.append("<option>Pasirinkite...</option>")

            $.each(data, function(key,value) {
                $el.append($("<option></option>").attr("value", key).text(value));
            });
        });

        $.get("/countries/" + countryId + "/states", function( data ) {
            var $el = jQuery("#state");
            $el.empty();
            $el.append("<option>Pasirinkite...</option>")
            $.each(data, function(key,value) {
                $el.append($("<option></option>").attr("value", key).text(value));
            });
        });
    });
});
*/

$( document ).ready(function() {

    jQuery("#country").change(function () {
        const countryId = this.value

        if (countryId === '') {
            return;
        }

        $.get("/countries/" + countryId + "/cities", function( data ) {
            var $el = jQuery("#city");
            $el.empty();
            $el.append("<option value=''>Pasirinkite...</option>")
            $.each(data, function(key,value) {
                $el.append($("<option>Pasirinkite...</option>").attr("value", key).text(value));
            });
        });

        $.get("/countries/" + countryId + "/states", function( data ) {
            var $el = jQuery("#state");
            $el.empty();
            $el.append("<option value=''>Pasirinkite...</option>")
            $.each(data, function(key,value) {
                $el.append($("<option></option>")
                    .attr("value", key).text(value));
            });
        });
    });
});