function addCity(e) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/city/create', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function editCity(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/city/' + id + '/edit', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function deleteCity(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/city/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

$("#frmCity").validate({
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

        $.ajax({
            url: $('#frmCity').attr('action'),
            type: "POST",
            data: new FormData(form),
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                var message = res.message;
                if (res.status == 200) {
                    localStorage.setItem('message', message);
                    window.location = adminUrl + '/city';
                } else {
                    toastr.error(message);
                    $btn.html($btn.data('original-text'));
                    $btn.attr('disabled', false);
                }
            },
            error: function (err) {
                console.log(err);
                toastr.error('Ooops...Something went wrong. Please try again.');
                $btn.html($btn.data('original-text'));
                $btn.attr('disabled', false);
            }
        });
    }
});
$.validator.addMethod("alpha", function (value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z ]+$/);
});