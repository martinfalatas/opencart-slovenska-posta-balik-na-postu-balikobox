var spb_points_form_option = ''; //option selektu - dostupne vyberne miesta
var spb_point_li = '';
var spb_selected_point = null;
var spb_detail_of_city = '';

var spb_posta_points = [];




/**
 * Urobi zoznam vsetkych miest
 */
function selectRegionSpb(idRegion) {
    spb_point_li = '';

    $.each(spb_posta_points, function (key, value) {
        if (value.ID == idRegion) {
            spb_point_li += '<li class="list-group-item" id="' + value.ID + '">' +
            '<p><b>' + value.NAZOV + '</b><br>' +
            value.ADRESA.ULICA + ' č.' + value.ADRESA.CISLO + '<br />' + value.ADRESA.OBEC + '<br />' + value.PSC + '<br />Tel. ' + value.TELEFON + '</p>' +
            '</li>';

            spb_selected_point = value;
        }
    });

    $('#spb_submit_to_sp_point').attr('disabled', 'disabled');
    $('#spb_point_li').empty();
    $('#spb_detail_of_city').empty();
    $('#spb_point_li').append(spb_point_li);

    getDetailedInfoSpb(idRegion);
}


function getPauseSpb(value) {
    var separator = ' : ';
    var time_str = '';

    var open = '';
    var pauza = '';

    $.each(value, function (key, value) {
        switch (key) {
            case 'OD':
                open += value;
                break;
            case 'DO':
                open += separator + value;
                break;

            case 'PRESTAVKA1_OD':
                pauza += value;
                break;
            case 'PRESTAVKA1_DO':
                pauza += separator + value;
                break;
            default :
                time_str += '';
        }
    });
    return '<td>' + open + '</td><td>' + pauza + '<td>';
}


function getOpeningHoursSpb() {
    var hours = '<table class="table"><tr><th></th><th>Otváracia doba</th><th>Prestávka</th></tr>';
    $.each(spb_selected_point['HODINY_PRE_VEREJNOST'], function (key, value) {
        hours += '<tr><td>' + key + '</td>';
        hours += getPauseSpb(value);
        hours += '</tr>'
    });

    hours += '</table>';
    return hours;
}

function getDetailedInfoSpb(target) {
    var current_gp = target;
    var info_blok = $('#spb_detail_of_city');
    spb_detail_of_city = '';

    $.each(spb_posta_points, function (key, value) {
        if (value.ID == current_gp) {
            spb_selected_point = value;
            spb_detail_of_city += '<li class="list-group-item list-group-item-info" id="detail-' + value.ID + '">' +
            getOpeningHoursSpb() +
            '</li>';
        }
    });

    $('#spb_point_li').find('li').addClass('active');
    $('#spb_submit_to_sp_point').removeAttr('disabled');

    info_blok.empty();
    info_blok.append(spb_detail_of_city);
}

function storeToPointSpb() {
    if ($('#spb_submit_to_sp_point').data('status')) {
        $('textarea[name="comment"]').val('[Balík na poštu; ' +
            spb_selected_point.NAZOV + ', ' +
            spb_selected_point.ADRESA.ULICA + ' č.' +
            spb_selected_point.ADRESA.CISLO + ', ' +
            spb_selected_point.PSC + ']\n'
        );
        $('.modalSpb').modal('hide');
    }
}


/**
 * Vyhlada mesto podla psc
 */
function selectByPscSpb() {
    var psc_number = $('#modal_postcode').val();

    spb_detail_of_city = "";
    $.each(spb_posta_points, function (key, value) {
        if (value.PSC == psc_number) {

            spb_selected_point = value;

            selectRegionSpb(value.ID);
            first_region = value.ID;

            $('#modal_region').find('option').removeAttr('selected');
            jQuery("option[value='" + first_region + "']").prop('selected', true);

            $('#modal_postcode').val("");
        }
    });

}


$(document).on('hidden.bs.modal', '.bs-modal-lg', function (e) {
    var textarea = $('textarea[name="comment"]');
    var point_radio = $('input[value="slovak_post_address.slovak_post_address"]');
    var input = jQuery("input[name='shipping_method']");
    if (!( point_radio.is(":checked") && (textarea.val().indexOf('Balík na poštu') ) > 0)) {
        point_radio.removeAttr("checked");
    }
});


$.ajax({
    url: 'index.php?route=extension/slovak_post_address/fillPoints',
    type: 'post',
    dataType: 'json',
    beforeSend: function () {
        $('#button-shipping-method').button('loading');
    },
    complete: function () {
        $('#button-shipping-method').button('reset');
        jQuery("input[value='slovak_post_address.slovak_post_address']").removeAttr('disabled');
    },
    success: function (json) {
        spb_posta_points = json;
    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
});



jQuery(document).ready(function () {
//////////////////////////////////

    $('input[value="slovak_post_address.slovak_post_address"]').removeAttr("checked");


    function deleteComment() {
        var textarea = $('textarea[name="comment"]');
        if ((textarea.val().indexOf('Balík na poštu')) > 0) {
            textarea.val('');
        }
    }

    var checked_status = false;
    var geis_radio = jQuery("input[value='slovak_post_address.slovak_post_address']");
    geis_radio.attr('disabled', true);
    if (geis_radio.not(':checked')) {
        deleteComment();
    } else {
        checked_status = true;
    }

    var input = jQuery("input[name='shipping_method']");
    input.on('click', function () {
        if ($(this).attr('value') != 'slovak_post_address.slovak_post_address') {
            if (checked_status) {
                deleteComment();
                checked_status = false;
            }
        } else {
            checked_status = true;
        }
    });

////////////////////////////////////////

    jQuery("input[name=shipping_method]").click(function () {

        value = $(this).val();
        if (value === 'slovak_post_address.slovak_post_address') {

            //okno môže byť zatvorené a opatovne otvorené, v tom prípade nech sú zachované posledné hodnoty
            var first_region = false;
            var old_value = $('[id="modal_region"]').val();
            if (old_value > 0) {
                first_region = old_value;
            } else {
                first_region = false;
            }

            //var selectedSity = false; //objekt zo svoleným mestom
            $.each(spb_posta_points, function (key, value) {
                if (!first_region) {
                    first_region = value.ID;
                }
                spb_points_form_option += '<option value=' + value.ID + '>' + value['NAZOV'] + ' (' + value.ADRESA.ULICA + ' ' + value.ADRESA.CISLO + ', ' + value.ADRESA.OBEC + ', ' + value.PSC + ')</option>';
            });

            selectRegionSpb(first_region);


            var img = $('' +
                '<div class="modal modalSpb fade bs-modal-lg in" role="dialog" tabindex="-1" aria-labelledby="myLargeModalLabelSpb"> ' +
                '<div class="modal-dialog modal-lg" role="document">' +

                '<div class="modal-content">' +
                '<div class="modal-header">' +

                '<div class="row-fluid">' +
                '<div class="col-sm-12">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>' +
                '</div>' +
                '</div>' +


                '<div class="row-fluid">' +
                '<div class="col-sm-2">' +
                '<img src="catalog/view/theme/default/image/slovenska_posta.jpg" class="img-responsive" alt="">' +
                '</div>' +

                '<div class="col-sm-8">' +
                '<h2 class="modal-title text-center" id="myLargeModalLabelSpb" style="text-align: center !important;">Balík na poštu a BalíkoBOX</h2>' +
                '</div>' +

                '<div class="col-sm-2" style="text-align: right;">' +
                '<img src="catalog/view/theme/default/image/balik_na_skpostu.jpg" class="img-responsive" alt="">' +
                '</div>' +
                '</div>' +


                '</div>' +

                '<div class="modal-body">' +
                '<form> ' +


                '<div class="row">' +

                '<div class="col-sm-12">' +
                '<div class="alert alert-normal">' +
                '</div>' +
                '</div>' +


                '<div class="col-sm-6">' +
                '<div class="form-group ui-widget">' +
                '<label for="modal_region">Mesto/Obec</label>' +
                '<select class="form-control" id="modal_region" onchange="selectRegionSpb(this.value)">' +
                spb_points_form_option +
                '</select>' +
                '</div>' +
                '</div>' +


                '<div class="col-sm-4">' +
                '<div class="form-group">' +
                '<label for="modal_postcode">PSČ</label>' +
                '<input class="form-control" id="modal_postcode" autocomplete="off" type="text">' +
                '</div>' +
                '</div>' +

                '<div class="col-sm-2">' +
                '<label>&nbsp;</label>' +
                '<button type="button" data-loading-text="Nahrávam ..." class="btn btn-primary btn-block" data-status="true" onclick="selectByPscSpb()">Vyhľadať</button>' +
                '</div>' +

                '</div>' +


                '<div class="row">' +
                '<div class="form-group"> ' +
                '<div class="col-sm-6">' +
                '<ul class="list-group" id="spb_point_li">' +
                spb_point_li +
                '</ul>' +
                '</div>' +
                '</div>' +

                '<div class="form-group"> ' +
                '<div class="col-sm-6">' +
                '<ul class="list-group" id="spb_detail_of_city">' +
                spb_detail_of_city +
                '</ul>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</form>' +
                '</div>' +

                '<div class="modal-footer"> ' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
                '<button type="button" class="btn btn-primary" data-status="true" disabled="disabled" id="spb_submit_to_sp_point" onclick="storeToPointSpb()">Poslať môj balík na zvolenú poštu</button> ' +
                '</div>' +

                '</div>' +
                '</div>' +
                '</div>'
            );

            if ($('.modalSpb').length < 1) {
                img.appendTo('body');
            }
            jQuery('.modalSpb').modal('show');
        }
    });
});