function addRole(e) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/roles/create', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function editRole(e, uuid) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/roles/' + uuid + '/edit', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

$(function () {
    $("#frmRole").validate({
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
                url: $('#frmRole').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        //toastr.success(message);
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/roles';
                        /*
                                                showHideLoader('hide');
                        */
                    } else {
                        toastr.error(message);
                        $btn.button('reset');
                        $btn.html($btn.data('original-text'));
                        $btn.attr('disabled', false);
                        /*
                                                showHideLoader('hide');
                        */
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

$('#checkedAll').on('click', function (e) {

    if ($(this).is(':checked', true)) {
        $(".sub_chk").prop('checked', true);
    } else {
        $(".sub_chk").prop('checked', false);
    }
});

$('#checkedPOSAll').on('click', function (e) {

    if ($(this).is(':checked', true)) {
        $(".pos_check").prop('checked', true);
    } else {
        $(".pos_check").prop('checked', false);
    }
});

$("input[type='checkbox'].sub_chk").change(function(){
    var a = $("input[type='checkbox'].sub_chk");
    if(a.length == a.filter(":checked").length){
        $('#checkedAll').prop("checked", true);
    } else {
        $('#checkedAll').prop("checked", false);
    }
});

$("input[type='checkbox'].pos_check").change(function(){
    var a = $("input[type='checkbox'].pos_check");
    if(a.length == a.filter(":checked").length){
        $('#checkedPOSAll').prop("checked", true);
    } else {
        $('#checkedPOSAll').prop("checked", false);
    }
});