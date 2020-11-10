$(function () {
    $("#frmRac").validate({
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
                url: $('#frmRac').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/rac';
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

function deleteRac(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/rac/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function addRac(e) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/rac/create', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function editRac(e, uuid) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/rac/' + uuid + '/edit', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}