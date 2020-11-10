$(function () {
    $("#frmInventory").validate({
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
            /*open_from: {
                check_from_time : true
            },*/
            closed_on: {
                check_closed_time: true
            },
        },
        messages: {},
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmInventory').attr('action'),
                type: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    var message = res.message;
                    if (res.status == 200) {
                        localStorage.setItem('message', message);
                        window.location = adminUrl + '/product_inventory';
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

function getProductBranch(e) {
    var product_id = $(e).val();
    var option = '<option value="">Select Branch</option>';
    if (product_id != '') {
        $.get(adminUrl + '/product_inventory/' + product_id + '/product_branch', function (response) {
            console.log(response);
            if (response.status == 200) {
                $('#hac_rac_product').val(response.has_rac);
                //if(response.has_rac == 0){
                    $('#rac_select').hide();
                    $('#rac_box_select').hide();
                //}
                $.each(response.list, function (k, v) {
                    option += '<option value="' + v.branch_id + '">' + v.name + '</option>';
                });
                $('#branch_id').html(option);
            }
        });

    }
}

function getBranchRac(e) {
    var branch_id = $(e).val();
    var has_product = $('#hac_rac_product').val();
    var option = '<option value="">Select Rac</option>';
    if (has_product > 0) {
        if (branch_id != '') {
            $.get(adminUrl + '/product_inventory/' + branch_id + '/product_rac', function (response) {
                console.log(response);
                if (response.status == 200) {

                    $.each(response.list, function (k, v) {
                        option += '<option value="' + v.rac_id + '">' + v.name + '</option>';
                    });
                    $('#rac_id').html(option);
                    $('#rac_select').show();
                    //$('#rac_box_select').show();

                }
            });

        }
    } else {
        $('#rac_select').hide();
        $('#rac_box_select').hide();
    }
}

function getRacBox(e) {
    var rac_id = $(e).val();
    var option = '<option value="">Select Box</option>';

        if (rac_id != '') {
            $.get(adminUrl + '/product_inventory/' + rac_id + '/product_rac_box', function (response) {
                console.log(response);
                if (response.status == 200) {

                    $.each(response.list, function (k, v) {
                        option += '<option value="' + v.box_id + '">' + v.name + '</option>';
                    });
                    $('#box_id').html(option);
                    $('#rac_box_select').show();

                }
            });

        }

}

function onlyNumberKey(evt) {

    // Only ASCII charactar in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}

function deleteProductInventory(e, uuid) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/product_inventory/' + uuid + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}