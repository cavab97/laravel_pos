$(function () {
    $("#frmResetPassword").validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            //element.closest('.form-group').append(error);
            if (element.hasClass('custom-input')) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2')) {
                error.insertAfter(element.parent().find('span.select2'));
            } else if (element.parent('.input-group').length ||
                element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent());
                // else just place the validation message immediatly after the input
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            new_password: {
                minlength: 8
            },
            confirm_password: {
                minlength: 8,
                equalTo: "#new_password"
            },

        },
        messages: {
            new_password: {
                minlength: "Your password must contain more than 8 characters",
            },
            confirm_password: {
                minlength: "Your password must contain more than 8 characters",
                equalTo: "Your Passwords Must Match"

            },
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.button('loading');
            $.ajax({
                url: $('#frmResetPassword').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        toastr.success(message);
                    } else {
                        toastr.error(message);
                        $btn.button('reset');
                    }
                },
                error: function (err) {
                    toastr.error('Ooops...Something went wrong. Please try again.');
                    $btn.button('reset');
                }
            });
        }
    });
    $.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0;
    }, "White space is not allowed");
    $.validator.addMethod("pwcheck", function (value, element) {
        return /^(?=.*\d)(?=.*[a-z])[0-9a-zA-Z]{6,}$/.test(value);
    }, "At least one number, then one letter or");
});