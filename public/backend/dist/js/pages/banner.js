$(function () {
    $("#frmBanner").validate({
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
            var checkDelete = $('input[name="_method"]').val();
            var description = '';
            var plainText = '';
            if (checkDelete != 'DELETE') {
                description = CKEDITOR.instances['description'].getData();
                $('#description').val(description);
                plainText = CKEDITOR.instances['description'].document.getBody().getText();
                plainText = plainText.trim();
            }

            /*if (checkDelete != 'DELETE' && plainText == '') {
                toastr.error('Description must be required.');
            } else {*/
                var $btn = $('#btnSubmit');
                $btn.html($btn.data('loading-text'));
                $btn.attr('disabled', true);
                $('.alert').hide();
                $.ajax({
                    url: $('#frmBanner').attr('action'),
                    type: "POST",
                    data: new FormData(form),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (res) {
                        var message = res.message;
                        if (res.status == 200) {
                            localStorage.setItem('message', message);
                            window.location = adminUrl + '/banner';
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
            //}
        }
    });
})
;


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

function previewWebImage(input) {
    if (input.files && input.files[0]) {
        var filerdr = new FileReader();
        filerdr.onload = function (e) {
            $("#web_icon_uploaded").css('display', 'none');  // hide if edit
            $("#web_icon_preview").css('display', 'block');
            $('#web_profile_preview').attr('src', e.target.result);
        };
        filerdr.readAsDataURL(input.files[0]);
    }
}

/*var _URL = window.URL || window.webkitURL;
$("#banner_for_mobile").change(function (e) {
    var file, img;
    if ((file = this.files[0])) {
        img = new Image();
        var objectUrl = _URL.createObjectURL(file);
        img.onload = function () {
            if (this.width >= 299 && this.height >= 156) {
                return true;
            } else {
                toastr.error('Only allowed 299X156 size file.');
                $("#banner_for_mobile").val('');
                $("#icon_preview").css('display', 'none');
                return false;
            }
            _URL.revokeObjectURL(objectUrl);
        };
        img.src = objectUrl;
    }
});
$("#banner_for_web").change(function (e) {
    var file, img;
    if ((file = this.files[0])) {
        img = new Image();
        var objectUrl = _URL.createObjectURL(file);
        img.onload = function () {
            if (this.width >= 1366 && this.height >= 234) {
                return true;
            } else {
                toastr.error('Only allowed 1366X234 size file.');
                $("#banner_for_web").val('');
                $("#web_icon_preview").css('display', 'none');
                return false;
            }
            _URL.revokeObjectURL(objectUrl);
        };
        img.src = objectUrl;
    }
});*/