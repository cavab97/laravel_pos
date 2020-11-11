$(function () {
    $("#frmRegister").validate({
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
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
            $(element).removeClass('is-valid');

        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');

        },
        rules: {
            reg_password: {
                minlength: 8
            },
            confirm_password: {
                minlength: 8,
                equalTo: "#reg_password"
            },

        },
        messages: {
            reg_password: {
                minlength: "Your password must contain more than 8 characters",
            },
            confirm_password: {
                minlength: "Your password must contain more than 8 characters",
                equalTo: "Your Passwords Must Match"

            },
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnLogin');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);

            $.ajax({
                url: $('#frmRegister').attr('action'),
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
                        window.location = res.url;
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
});

function onlyNumberKey(evt) {

    // Only ASCII charactar in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}