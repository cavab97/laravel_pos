$(function () {

    $("#frmVoucher").validate({
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
            "voucher_products[]": {
                required: function(element) {
                    return $("#voucher_categories").val() == '';
                }
            },
            "voucher_categories[]": {
                required: function(element) {
                    return $("#voucher_products").val() == '';

                }
            },

        },
        messages: {
            "voucher_categories[]":{
                required:"Select either category or product"
            },
            "voucher_products[]":{
                required:"Select either category or product"
            }
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmVoucher').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/voucher';
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

function deleteVoucher(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/voucher/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function checkPercentage(e) {
    var type = $('#voucher_discount_type').val();
    var input = $(e).val();
    if (type == 2) {
        if (input > 100) {
            toastr.error('Percentage value not greater then 100');
            $(e).val('');
        }
    }
}
