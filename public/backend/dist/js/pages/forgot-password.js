$(function () {
    $("#frmForgot").validate({
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
            $(element).removeClass('is-valid');
            if ($(element).find('check-email').length == 0) {
                $('#email').removeClass('mb-3');
            }
            if ($(element).find('check-pass').length == 0) {
                $('#password').removeClass('mb-3');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
            if ($(element).find('check-email').length == 0) {
                $('#email').addClass('mb-3');
            }
            if ($(element).find('check-pass').length == 0) {
                $('#password').addClass('mb-3');
            }
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();

            $.ajax({
                url: $('#frmForgot').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {

                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message',message);
                        //toastr.success('Welcome , Logged in...');
                        window.location = adminUrl + '/';
                    } else {
                        toastr.error(message);
                        //$('.alert-danger').show().html(message);
                        $btn.button('reset');
                        $btn.html($btn.data('original-text'));
                        $btn.attr('disabled', false);
                    }
                },
                error: function (err) {
                    toastr.error('Ooops...Something went wrong. Please try again.');
                    //$('.alert-danger').show().html('Ooops...Something went wrong. Please try again.');
                    $btn.button('reset');
                    $btn.html($btn.data('original-text'));
                    $btn.attr('disabled', false);
                }
            });
        }
    });


    $("#frmResetPassword").validate({
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
            $(element).removeClass('is-valid');
            if ($(element).find('check-email').length == 0) {
                $('#email').removeClass('mb-3');
            }
            if ($(element).find('check-pass').length == 0) {
                $('#password').removeClass('mb-3');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
            if ($(element).find('check-email').length == 0) {
                $('#email').addClass('mb-3');
            }
            if ($(element).find('check-pass').length == 0) {
                $('#password').addClass('mb-3');
            }
        },
        rules: {
            password: {
                minlength: 6,
            },
            confirm_password: {
                minlength: 6,
                equalTo: "#password",
            }
        },
        messages: {
            password: {
                minlength: "Your password must contain more than 6 characters",
            },
            confirm_password: {
                minlength: "Your password must contain more than 6 characters",
                equalTo: "Enter confirm password same as password" // custom message for mismatched passwords
            }
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();

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
                        localStorage.setItem('message',message);
                        setTimeout(function () {
                            window.location.href = adminUrl + '/';
                        }, 2000);
                    } else {
                        toastr.error(message);
                    }
                    $btn.button('reset');
                    $btn.html($btn.data('original-text'));
                    $btn.attr('disabled', false);
                },
                error: function (err) {
                    console.log(err);
                    toastr.error('Ooops...Something went wrong. Please try again.');
                    $btn.button('reset');
                    $btn.html($btn.data('original-text'));
                    $btn.attr('disabled', false);
                }
            });
        }
    });
});
