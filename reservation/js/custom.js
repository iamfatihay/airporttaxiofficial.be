$(document).ready(function () {
    'use strict';
    $('.js-example-basic-single').select2();
    // $('.select2-container').css('width', '100%');
    
})

function inverseInputAddress() {
    console.log('sdsa');
    if ($('#pu_pu').css('display') == "block") {
        origin_is_airport_dest_is_address();
    }
    else {
        origin_is_add_dest_is_airport();
    }
}
function origin_is_add_dest_is_airport() {
    $('#pu_pu').css('display', 'block');
    $('#pu_air').css('display', 'none');
    $('#dest2air2').css('display', 'block');
    $('#dest2dest2').css('display', 'none');

    document.getElementById("inputAir").required = false;
    document.getElementById("autocomplete_address_arrival").required = false;
    document.getElementById("autocomplete_address_departure").required = true;
    document.getElementById("inputAir3").required = true;
}
function origin_is_airport_dest_is_address() {
    $('#pu_pu').css('display', 'none');
    $('#pu_air').css('display', 'block');
    $('#dest2air2').css('display', 'none');
    $('#dest2dest2').css('display', 'block');

    document.getElementById("autocomplete_address_departure").required = false;
    document.getElementById("inputAir3").required = false;

    document.getElementById("inputAir").required = true;
    document.getElementById("autocomplete_address_arrival").required = true;
}

function puNdest() {
    console.log('entering puNdest()');
    
    var pickup;
    var dest;
    if ($('#pu_pu').css('display') == "block") {
        pickup = $("#autocomplete_address_departure").val();
        dest = $('#inputAir3').val();
        console.log('je passe en 1');
    }
    else {
        pickup = $('#inputAir').val();
        dest = $("#autocomplete_address_arrival").val();
    }
    $('#fpickup').val(pickup);
    $('#fdestin').val(dest);
    return true;
}
    
