$(function () {
    $("#frmProduct").validate({
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


            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmProduct').attr('action'),
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
                        window.location = adminUrl + '/product';
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


function addAttribute() {
    var inputCount = $('#attribute_count');
    var num = inputCount.val();


    var att_value = $("#att_category_id").find("option:selected").val();
    var att_text = $("#att_category_id").find("option:selected").text();

    var attribute = $('#attribute').val();
    var is_enabled = $('#is_enabled').val();

    var att_price = $('#att_price').val();

    var no = $('#no').val();
    var yes = $('#yes').val();
    var existsFlag = true;
    if (att_value == '') {
        toastr.error($('#select_att_error').text());
        $('#exists_error').hide();
        return false;
    }
    $('.cat_att_id').each(function (k, v) {
        var text_value = $(v).val();

        if (text_value == att_value) {
            existsFlag = false;
            toastr.error($('#exists_error').text());
            //$('#exists_error').show();
            //$('#select_att_error').hide();
            return false;
        }
    });
    if (existsFlag) {
        var html = '';
        $('#select_att_error').hide();
        $('#exists_error').hide();
        var flt;
        flt = 'float-right';
        var attribute_html = '';
        /* Get category Attribute */
        $.get(adminUrl + '/product/category-attribute/' + att_value, function (response) {
            var j = 1;
            for (var i = 0; i < response.data.length; i++) {
                console.log(response.data[i]);
                //attribute_html.push(response.data[i]);

                attribute_html += '<tr>';
                attribute_html += '<td>' + response.data[i].name + '</td>';
                attribute_html += '<td>';
                attribute_html += '<input type="hidden" class="att_id" id="att_id_' + num + '" value="' + response.data[i].attribute_id + '" name="attribute_id_[' + att_value + '][]">';
                attribute_html += '<input type="number" name="att_price_' + response.data[i].attribute_id + '[' + att_value + '][]" value="" id="att_price_' + att_value + i + '" class="form-control form-control-sm att_price" placeholder="' + att_price + '" required min="0">';
                attribute_html += '</td>';
                attribute_html += '</tr>';

                j++;
            }
            html += '<div class="col-md-6 table-responsive" id="attribute_detail_div_' + num + '">';
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead class="thead-light">';
            html += '<tr class="text-center">';
            html += '<th colspan="2">' + att_text;
            html += '<a href="javascript:void(0);" class="text-dark"><i onclick="removeAtt(' + num + ')" class="fa fa-times float-right mt-1" aria-hidden="true"></i></a>';
            html += '<input type="hidden" class="cat_att_id" id="cat_att_id_' + num + '" value="' + att_value + '" name="cat_attribute_id[]">';
            html += '<input type="hidden" id="selectedText_' + num + '" value="' + att_text + '">';
            html += '</th>';
            html += '</tr>';
            html += '</thead>';

            html += '<tbody style="border-bottom: 2px solid #dee2e6;">';
            html += attribute_html;
            html += '</tbody>';
            html += '</table>';
            html += '</div>';


            $('#multi-attributeBlock').append(html);
            inputCount.val(parseInt(num) + 1);

            $("#att_category_id").val('').change();

        });


    }
}


function removeAtt(num) {
    var inputCount = $('#attribute_count');
    var num_count = inputCount.val();
    $('#attribute_detail_div_' + num).remove();
}

function addModifier() {
    var inputCount = $('#modifier_count');
    var num = inputCount.val();


    var mod_value = $("#modifier_id").find("option:selected").val();
    var mod_text = $("#modifier_id").find("option:selected").text();

    var modifier = $('#modifier').val();
    var is_enabled = $('#is_enabled').val();

    var mod_price = $('#mod_price').val();

    var no = $('#no').val();
    var yes = $('#yes').val();
    var existsFlag = true;
    if (mod_value == '') {
        toastr.error($('#select_mod_error').text());
        $('#mod_exists_error').hide();
        return false;
    }
    $('.mod_id').each(function (k, v) {
        var text_value = $(v).val();

        if (text_value == mod_value) {
            existsFlag = false;
            toastr.error($('#mod_exists_error').text());
            //$('#mod_exists_error').show();
            //$('#select_mod_error').hide();
            return false;
        }
    });
    if (existsFlag) {
        var html = '';
        $('#select_mod_error').hide();
        $('#mod_exists_error').hide();
        var lang = $('#lang').val();
        var flt;

        flt = 'float-right';

        html += '<div class="col-md-6 table-responsive" id="modifier_detail_div_' + num + '">';
        html += '<table class="table table-sm table-bordered mb-2">';
        html += '<tbody style="border-bottom: 2px solid #dee2e6;">';
        html += '<thead class="thead-light">';
        html += '<tr class="text-center">';
        html += '<th colspan="2">' + mod_text;
        html += '<a href="javascript:void(0);" class="text-dark"><i onclick="removeMod(' + num + ')" class="fa fa-times float-right mt-1" aria-hidden="true"></i></a>';
        html += '<input type="hidden" class="mod_id" id="mod_id_' + num + '" value="' + mod_value + '" name="modifier_id[' + num + ']">';
        html += '<input type="hidden" id="selectedText_mod' + num + '" value="' + mod_text + '">';
        html += '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tr>';
        html += '<td>';
        html += '<label for="mod_price_' + num + '">' + mod_price + '</label>';
        html += '</td>';
        html += '<td>';
        html += '<input type="number" name="mod_price[' + num + ']" value="" id="mod_price_' + num + '" class="form-control form-control-sm" placeholder="' + mod_price + '" required min="0">';
        html += '</td>';
        html += '</tr>';
        html += '<tr>';
        html += '<td>';
        html += '<label for="is_enabled_' + num + '">' + is_enabled + '</label>';
        html += '</td>';
        html += '<td>';
        html += '<span class="radio-left ml-2"><input type="radio" name="is_enabled_mod[' + num + ']" value="1" checked/>' + yes + '</span>';
        html += '<span class="radio-right ml-2"><input type="radio" name="is_enabled_mod[' + num + ']" value="0"/>' + no + '</span>';
        html += '</td>';
        html += '</tr>';
        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        $('#multi-modifierBlock').append(html);
        inputCount.val(parseInt(num) + 1);

        $("#modifier_id").val('').change();
    }
}


function removeMod(num) {
    var inputCount = $('#modifier_count');
    var num_count = inputCount.val();
    $('#modifier_detail_div_' + num).remove();
}

function switchDisable(e, id) {
    var stock_required = $("#stock_level_requires").val();
    if ($(e).prop('checked')) {
        $("#display_order_" + id).attr('required', true);
        $("#display_order_" + id).attr('disabled', false);
        if (stock_required == "true") {
            $("#warning_stock_level_" + id).attr('disabled', true);
        } else {
            $("#warning_stock_level_" + id).attr('disabled', false);
            $("#warning_stock_level_" + id).val(0);
        }
        //$("#warning_stock_level_" + id).attr('disabled', false);
        $("input[name='is_enabled_status[" + id + "]']").attr("disabled", false);

        $("#printer_id" + id).attr('required',true);
        $("#printer_id" + id).attr('disabled',false);

    } else {
        $("#display_order_" + id).val('');
        $("#display_order_" + id).removeClass('is-valid');
        $("#display_order_" + id).removeClass('is-invalid');
        $("#display_order_" + id).attr('required', false);
        $("#display_order_" + id).attr('disabled', true);

        $("#warning_stock_level_" + id).val('');
        $("#warning_stock_level_" + id).removeClass('is-valid');
        $("#warning_stock_level_" + id).removeClass('is-invalid');
        $("#warning_stock_level_" + id).attr('required', false);
        $("#warning_stock_level_" + id).attr('disabled', true);

        $("input[name='is_enabled_status[" + id + "]']").attr("disabled", true);

        $("#printer_id" + id).val('');
        $("#printer_id" + id).removeClass('is-valid');
        $("#printer_id" + id).removeClass('is-invalid');
        $("#printer_id" + id).attr('required',false);
        $("#printer_id" + id).attr('disabled',true);
    }
}

function deleteProduct(e, uuid) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/product/' + uuid + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function addImage() {
    var imageHtml = '';
    var num = $('#product_image_count').val();
    imageHtml += '<div id="image_' + num + '" class="col-3 validateImage">  ';
    imageHtml += '<div class="form-group">';
    imageHtml += '<div class=""><input type="file" data-number="' + num + '" style="margin: 7px 0 7px 0;"  name="product_images[]" onchange="partImagePreview(this,\'' + num + '\')" id="customFile_' + num + '" class="form-control"/><label class="custom-file-label selected" style="margin: 7px;" for="customFile_' + num + '"></label></div>';
    imageHtml += '<div class="prev_img" id="product_image_preview_' + num + '" style="border: 1px solid #eae7e79e;"><button type="button" class="btn btn-danger btn-sm btn-sm btn-block" onclick="removePartImage(' + num + ')"><span class="fa fa-picture-o"></span> Remove</button></div>';
    imageHtml += '</div>';
    imageHtml += '<div class="input-error partimageerror' + num + '" class="text-danger"></div>';
    imageHtml += '</div>';
    $('#product_image_count').val(parseInt(num) + 1);
    $('#multi-imageBlock').append(imageHtml);

}

var _URL = window.URL || window.webkitURL;

function partImagePreview(input, num) {
    $('#partimageerror').show().html('');
    $('#product_image_preview_' + num).html('');
    if (input.files && input.files[0]) {
        var filerdr = new FileReader();
        filerdr.readAsDataURL(input.files[0]);
        var fileType = input.files[0].type;
        fileType = fileType.split('/');
        var exts = ['jpg', 'png', 'jpeg'];
        var png_jpg_msg = $('#png_jpg_msg').val();
        if ($.inArray(fileType[1].toLowerCase(), exts) > -1) {
            filerdr.onload = function (e) {
                /*var image = new Image();
                image.src = e.target.result;
                image.onload = function () {
                    if (this.height <= 400 && this.width <= 400) {*/
                        var html = '<img src="' + e.target.result + '" class="img-thumbnail productImgPrev"/>';
                        html += '<button type="button" class="btn btn-danger btn-sm btn-sm btn-block" onclick="removePartImage(' + num + ')"><span class="fa fa-picture-o"></span> Remove</button>';
                        $('#product_image_preview_' + num).html(html);
                        $('.partimageerror' + num).html('');
                        $('.partimageerror' + num).hide();
                    /*} else {
                        toastr.error('Only allowed 400X400 size file.');
                        $(input).val('');
                        return false;
                    }*/

            };
            filerdr.readAsDataURL(input.files[0]);
        } else {
            $('.partimageerror' + num).html(png_jpg_msg);
            $(input).val('');
            return false;
        }
    }
}

function removePartImage(num) {
    $('#image_' + num).remove();
}

function removeEditImage(id, imageName, key) {
    $.get(adminUrl + '/product/delete-image/' + id, function (response) {
        var message = response.message;
        if (response.status == 200) {
            //toastr.success(message);
            $('#image_' + key).remove();
        } else {
            toastr.error(message);
        }
    });
}

function hasRac(e) {

    if ($(e).prop('checked')) {
        $(".with_rac").css('display', 'block');
        $(".without_rac").css('display', 'none');
        $(".without_rac_category").find("option:selected").val()
    } else {
        console.log('else');
        $(".with_rac").css('display', 'none');
        $(".without_rac").css('display', 'block');
        $(".with_rac_category").find("option:selected").val()

    }
}


