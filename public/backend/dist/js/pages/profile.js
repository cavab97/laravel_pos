$(function () {
    $.validator.addMethod('checkoldpassword', function (value, element) {
        var newPassword = $('#password').val();
        var confirmPassword = $('#confirm_password').val();

        if (newPassword) {
            if (!confirmPassword) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }, '');

    $("#frmProfile").validate({
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
            password: {
                checkoldpassword: true,
            },
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
            $btn.button('loading');

            $.ajax({
                url: $('#frmLogin').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        toastr.success(message);
                    } else {
                        toastr.error(message);
                    }
                    $btn.button('reset');
                },
                error: function (err) {
                    toastr.error('Ooops...Something went wrong. Please try again.');
                    $btn.button('reset');
                    $btn.attr('disabled', false);
                }
            });
        }
    });
});
