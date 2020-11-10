function onlyNumberKey(evt) {

    // Only ASCII charactar in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var filerdr = new FileReader();
        filerdr.onload = function (e) {
            $("#icon_uploaded").css('display', 'none');  // hide if edit
            $("#icon_preview").css('display', 'block');
            $('#profile_preview').attr('src', e.target.result);
        };
        filerdr.readAsDataURL(input.files[0]);
    }
}

function deleteCustomer(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/customer/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

$(function () {
    $("#frmCustomer").validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
            if (element.hasClass('select2')) {
                error.insertAfter(element.parent().find('span.select2'));
            } else if (element.parent('.input-group').length ||
                element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent());
                // else just place the validation message immediatly after the input
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        rules: {
            confirm_password: {
                equalTo: "#password",
            }
        },
        messages: {
            confirm_password: {
                equalTo: "Enter confirm password same as password"
            }
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            $.ajax({
                url: $('#frmCustomer').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        toastr.success(message);
                        $("#user_uuid").val(res.uuid);
                        $(".nav.nav-tabs > li").removeClass("disabled");
                        $("#cus_info").removeClass("active");
                        $("#basicInfo").removeClass("active");
                        $("#cus_address").addClass("active");
                        $("#addressArea").addClass("active");
                        $btn.html($btn.data('original-text'));
                        $btn.attr('disabled', false);
                        var checkDelete = $('input[name="_method"]').val();

                        if (checkDelete == 'DELETE') {
                            window.location = adminUrl + '/customer';
                        }

                    } else {
                        toastr.error(message);
                        $btn.button('reset');
                        $btn.html($btn.data('original-text'));
                        $btn.attr('disabled', false);
                    }
                },
                error: function (err) {
                    toastr.error('Ooops...Something went wrong. Please try again.');
                    $btn.button('reset');
                    $btn.html($btn.data('original-text'));
                    $btn.attr('disabled', false);
                }
            });
        }
    })
    ;
})
;

$(function () {

    $("#frmCustomerAddress").validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
            if (element.hasClass('select2')) {
                error.insertAfter(element.parent().find('span.select2'));
            } else if (element.parent('.input-group').length ||
                element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent());
                // else just place the validation message immediatly after the input
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');

        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        rules: {
            latitude: {
                latCoord: true
            },
            longitude: {
                longCoord: true
            }
        },
        messages: {},

        focusInvalid: true,
        submitHandler:

            function (form) {
                var $btn = $('#btnSubmitAdd');
                $btn.html($btn.data('loading-text'));
                $btn.attr('disabled', true);
                $('.alert').hide();

                $.ajax({
                    url: $('#frmCustomerAddress').attr('action'),
                    type: "POST",
                    data: new FormData(form),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (res) {
                        var message = res.message;
                        if (res.status == 200) {
                            /*toastr.success(message);
                            $btn.html($btn.data('original-text'));
                            $btn.attr('disabled', false);*/
							localStorage.setItem('message', message);
							window.location = adminUrl + '/customer';
                        } else {
                            toastr.error(message);
                            $btn.html($btn.data('original-text'));
                            $btn.attr('disabled', false);
                        }
                    },
                    error: function (err) {
                        toastr.error('Ooops...Something went wrong. Please try again.');
                        $btn.html($btn.data('original-text'));
                        $btn.attr('disabled', false);
                    }
                });
            }
    })
    ;
    $.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "White space is not allowed");

    $.validator.addMethod('latCoord', function (value, element) {
        console.log(this.optional(element))
        return this.optional(element) ||
            value.length >= 4 && /^(?=.)-?((8[0-5]?)|([0-7]?[0-9]))?(?:\.[0-9]{1,20})?$/.test(value);
    }, 'Latitude format has error.');

    $.validator.addMethod('longCoord', function (value, element) {
        return this.optional(element) ||
            value.length >= 4 && /^(?=.)-?((0?[8-9][0-9])|180|([0-1]?[0-7]?[0-9]))?(?:\.[0-9]{1,20})?$/.test(value);
    }, 'Longitude format has error.');
    $.validator.addMethod('checkFirst', function (value, element) {
        return value.toString()[0] == 0 && value != "";
    }, 'Mobile number must start with "0"');

});

function checkLatitude(lat, num) {
    var result = lat.length >= 4 && /^(?=.)-?((0?[8-9][0-9])|180|([0-1]?[0-7]?[0-9]))?(?:\.[0-9]{1,20})?$/.test(lat);
    $("#lat_error_" + num).hide();
    $("#lat_error_" + num).removeClass();
    $("#lat_error_" + num).css('display', 'none');
    if (lat) {
        if (result == false) {
            $("#lat_error_" + num).html('Latitude format has error.');
            $("#lat_error_" + num).addClass('text-danger');
            $("#lat_error_" + num).css('display', 'block');
        }
    }
}

function checkLongitude(long, num) {
    var result = long.length >= 4 && /^(?=.)-?((0?[8-9][0-9])|180|([0-1]?[0-7]?[0-9]))?(?:\.[0-9]{1,20})?$/.test(long);
    $("#long_error_" + num).hide();
    $("#long_error_" + num).removeClass();
    $("#long_error_" + num).css('display', 'none');
    if (long) {
        if (result == false) {
            $("#long_error_" + num).html('Longitude format has error.');
            $("#long_error_" + num).addClass('text-danger');
            $("#long_error_" + num).css('display', 'block');
        }
    }
}

function addAddress(e) {
    var inputCount = $('#address_count');
    var num = parseInt(inputCount.val()) + 1;

    var address_line1 = $('#address_line1').val();
    var address_line2 = $('#address_line2').val();
    var is_default = $('#is_default').val();
    var longitude = $('#longitude').val();
    var latitude = $('#latitude').val();
    var status = $('#add_status').val();
    var add_address = $('#add_address').val();
    var remove_msg = $('#remove_msg').val();
    var yes = $('#yes').val();
    var no = $('#no').val();
    var active_status = $('#active_status').val();
    var deactive_status = $('#deactive_status').val();

    var address_line1_1 = $('#address_line1_1').val();
    var longitude_1 = $('#longitude_1').val();
    var latitude_1 = $('#latitude_1').val();

    var flag = true;
    if (address_line1_1 != '' && longitude_1 != '' && latitude_1 != '') {
        $("#address_display_message").css('display', 'none');
    } else {
        flag = false;
        toastr.error('Please filled-up all details');
        $('#address_display_message .alert-danger').show().html('Please filled-up all details');
    }
    if (flag == true) {
        var html = '';
        html += '<div class="card card_address_div_' + num + '">';
        html += '<div class="card-header">';
        html += '<h3 class="card-title">' + add_address + '</h3>';
        html += '<div class="float-right">';
        html += '<button type="button" onclick="removeAddress(' + num + ')" class="btn btn-danger btn-sm"><i class="fa fa-times"></i>' + remove_msg + '</button>';
        html += '</div>';
        html += '</div>';
        html += '<div class="card-body">';

        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<div class="form-group">';
        html += '<label for="latitude_' + num + '">' + latitude + '</label>';
        html += '<input type="text" name="latitude[' + num + ']" value="" id="latitude_' + num + '" class="form-control" placeholder="' + latitude + '" onblur="checkLatitude(this.value,' + num + ')">';
        html += '<span id="lat_error_' + num + '" style="display: none">Latitude format has error.</span>';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-6">';
        html += '<div class="form-group">';
        html += '<label for="longitude_' + num + '">' + longitude + '</label>';
        html += '<input type="text" name="longitude[' + num + ']" value="" id="longitude_' + num + '" class="form-control" placeholder="' + longitude + '" onblur="checkLongitude(this.value,' + num + ')">';
        html += '<span id="long_error_' + num + '" style="display: none">Longitude format has error.</span>';
        html += '</div>';
        html += '</div>';
        /*html += '<div class="col-md-2 mt-2">';
        html += '<button type="button" style="margin-top: 25px;padding: 7px;" onclick="getFullAddress(' + num + ')" class="btn btn-info btn-sm">Lookup</button>';
        html += '</div>';*/
        html += '</div>';

        html += '<div class="row">';
        html += '<div class="col-md-6 required">';
        html += '<div class="form-group">';
        html += '<label for="address_line1_' + num + '">' + address_line1 + '</label>';
        html += '<input type="text" name="address_line1[' + num + ']" value="" id="address_line1_' + num + '" class="form-control" placeholder="' + address_line1 + '" required>';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-6">';
        html += '<div class="form-group">';
        html += '<label for="address_line2_' + num + '">' + address_line2 + '</label>';
        html += '<input type="text" name="address_line2[' + num + ']" value="" id="address_line2_' + num + '" class="form-control" placeholder="' + address_line2 + '">';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<div class="form-group">';
        html += '<label for="is_default_' + num + '">' + is_default + '</label>';
        html += '<span class="radio-left"><input type="radio" id="y_' + num + '" name="is_default[' + num + ']" class="radio-yes" onclick="checkDefault(this,\'Yes\')" value="1"/>' + yes + '</span>';
        html += '<span class="radio-right"><input type="radio" id="n_' + num + '" name="is_default[' + num + ']" class="radio-no" onclick="checkDefault(this,\'No\')" value="0" checked/>' + no + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="row">';
        html += '<div class="col-md-6 required">';
        html += '<div class="form-group">';
        html += '<label for="status_' + num + '">' + status + '</label>';
        html += '<select name="status[' + num + ']" id="status_' + num + '" class="form-control" required>';
        html += '<option value="1">' + active_status + '</option>';
        html += '<option value="0">' + deactive_status + '</option>';
        html += '</select>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        console.log(html);
        $('#multi-addressBlock').append(html);
        inputCount.val(parseInt(num) + 1);
    }

}

function removeAddress(num) {
    var inputCount = $('#address_count');
    var num_count = inputCount.val();
    $('.card_address_div_' + num).remove();
}

function checkDefault(e, type) {
    var r_id = e.id;
    var arr = r_id.split('_');
    var name = arr[0];
    var num = arr[1];
    if (name == 'y') {
        $('.radio-yes').attr('checked', false);
        $('.radio-yes').each(function (k, v) {
            var key = k + 1;
            if (key == num) {
                $("#y_" + num).prop("checked", true);
            } else {
                $("#y_" + key).prop('checked', false);
                $("#n_" + key).prop("checked", true);
            }
        });
    } else {
        if (num != 1) {
            $('.radio-no').prop('checked', true);
            $("#" + r_id).prop("checked", true);
        }
    }
}