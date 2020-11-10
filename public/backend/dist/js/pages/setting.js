$(function () {

    $("#frmSetting").validate({
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
            type: {
                required: true
            },
            value: {
                required: true,
            }
        },
        messages: {
        },

        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();

            $.ajax({
                url: $('#frmSetting').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/setting';
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
    });

    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z_0-9 @./#&+-.:/!$%()=';{}"<>?]*$/.test(value);
    }, "Please enter only string");

    jQuery.validator.addMethod("float", function(value, element) {
        return this.optional(element) || /^[1-9]\d*(\.\d+)?$/.test(value);
    }, "Enter only float value");

    $.validator.addMethod("check_value_boolean", function(value, element) {
        return value === 'true' || value === 'false' || toString.call(value) === '[object Boolean]';

    }, "Enter only true OR false");

    $.validator.addMethod("check_value_color", function(value, element) {
        return this.optional(element) || /^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(value);
    }, "Enter valid color code");

    $.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "White space is not allowed");

    jQuery.validator.addMethod("time24", function(value, element) {
        /*if (!/^\d{2}:\d{2}:\d{2}$/.test(value)) return false;
        var parts = value.split(':');
        if (parts[0] > 23 || parts[1] > 59 || parts[2] > 59) return false;
        return true;*/
        //return /^([01]?[0-9]|2[0-3])(:[0-5][0-9]){2}$/.test(value);
        return /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/.test(value);
    }, "Invalid time format.");
});

$(function () {
    $('#type').on('change', function () {

        if($(this).val() != 4){
            $("#textBoxId").show();
            $("#selectBoxId").hide();
        }

        $('input[name="value"]').rules('remove');
        $('input[name="value"]').val('');
        $('input[name="value"]').attr("type",'text');
        $('input[name="value"]').removeAttr("min");
        $('input[name="value"]').removeAttr("max");

        if ($(this).val() == 1) {  // String
            $('input[name="value"]').attr("placeholder",'String');
            $('input[name="value"]').rules('add', {
                lettersonly: true,
                required: true,
            });
        } else if ($(this).val() == 2) {  // Integer
            $('input[name="value"]').attr("placeholder",'Integer');
            $('input[name="value"]').rules('add', {
                digits: true,
                required: true,
                messages:{
                    digits: 'Please enter only integer'
                }
            });
        } else if ($(this).val() == 3) {  // Float
            $('input[name="value"]').attr("placeholder",'Float');
            $('input[name="value"]').rules('add', {
                float: true,
                required: true
            });
        } else if ($(this).val() == 4) {  // Boolean
            $("#textBoxId").hide();
            $("#selectBoxId").show();
            /*$('input[name="value"]').attr("placeholder",'Boolean');
            $('input[name="value"]').rules('add', {
                check_value_boolean: true,
                required: true
            });*/
        } else if ($(this).val() == 5) {  // Color
            $('input[name="value"]').attr("placeholder",'#000000');
            $('input[name="value"]').rules('add', {
                check_value_color: true,
                required: true
            });
        } else if ($('#type').val() == 6) {  // minutes
            $('input[name="value"]').attr("placeholder",'minutes');
            $('input[name="value"]').attr("type",'number');
            $('input[name="value"]').attr("min",'1');
            $('input[name="value"]').attr("max",'1440');
            $('input[name="value"]').rules('add', {
                //time24: true,
                required: true,
                max: 1440,
                messages:{
                    max: 'Please enter a value less than or equal to 24*60 minutes.'
                }
            });
        }

        $('input[name="value"]').valid();  // trigger validation of the text field (optional)

    });
});

function addSetting(e) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/setting/create', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function editSetting(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/setting/' + id + '/edit', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}