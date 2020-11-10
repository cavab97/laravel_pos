$(function () {
    $("#frmBox").validate({
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
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmBox').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/box';
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
    });
});

function deleteBox(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/box/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function addBox(e) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/box/create', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function editBox(e, uuid) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/box/' + uuid + '/edit', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function getRac(branchId) {
    var option = '<option value="">Select Rac</option>';
    var prod_option = '<option value="">Select Product</option>';
    if (branchId) {
        $('#rac_id').html('<option value="">loading....</option>');
        $('#product_id').html('<option value="">loading....</option>');
        $.get(adminUrl + '/box/' + branchId + '/list', function (response) {
            if (response.status == 200) {
                /*Rac List*/
                $.each(response.list, function (k, v) {
                    option += '<option value="' + v.rac_id + '">' + v.name + '</option>';
                });
                $('#rac_id').html(option);

                /*Product List*/
                $.each(response.productList, function (k, v) {
                    prod_option += '<option value="' + v.product_id + '">' + v.name + '</option>';
                });
                $('#product_id').html(prod_option);
            }
        });
    } else {
        $('#rac_id').html(option);
        $('#product_id').html(prod_option);
    }
}

$(document).ready(function(){
    $('#box_for').on('change', function() {
        if ( this.value == '1') {
            $("#box_for_wine").show();
            $("#box_for_beer").hide();
        } else if ( this.value == '2') {
            $("#box_for_wine").hide();
            $("#box_for_beer").show();
        } else {
            $("#box_for_wine").hide();
            $("#box_for_beer").hide();
        }
    });
});