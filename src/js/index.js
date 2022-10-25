let $ = require("jquery");

window.onload = function() {

    $('.reset').click(function() {
        $('.card-option').each(function() {
            $(this).val('');
        });
    });

    $('.gen-button').click(function() {
        let number = $(this).attr('data-number');
        let params = new URLSearchParams();

        params.append('section', 'card');
        params.append('number',     number);
        params.append('border',     $("select[name='border']").val());
        params.append('marginw',    $("input[name='marginw']").val());
        params.append('marginh',    $("input[name='marginh']").val());
        params.append('brightness', $("input[name='brightness']").val());
        params.append('contrast',   $("input[name='contrast']").val());

        params.append("mw-"  + number, $("input[name='mw-" + number + "']").val());
        params.append("mh-"  + number, $("input[name='mh-" + number + "']").val());
        params.append("mx-"  + number, $("input[name='mx-" + number + "']").val());
        params.append("my-"  + number, $("input[name='my-" + number + "']").val());
        params.append("rot-" + number, $("input[name='rot-" + number + "']").val());

        params.append('offset', $("input[name='offset']").val());
        params.append('limit',  $("input[name='limit']").val());
        params.append('save', $("input[name='save']").prop("checked") ? "1" : "0");

        params.append('path', $("input[name='path']").val());

        $('img.card-' + number).attr("src", "/?" + params.toString());
    });

    $('.gen-button-all').click(function() {
        loadCards();
    });

    // Cargamos las imagenes que hayan
    loadCards();
};

function loadCards() {
    $('.gen-button').each(function() {
        $(this).trigger("click");
    });
}
