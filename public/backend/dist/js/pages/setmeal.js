$(function () {

    $.validator.addMethod('product', function(value, element, param) {
        var a = 0;
        //alert($('div[id^="product_detail_div_"]').length);
        $('div[id^="product_detail_div_"]').each(function(){
            a++;
        });
        if(a == 0){
            return false;
        } else {
            return true;
        }

    }, 'Invalid value');

    $("#frmSetmeal").validate({
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
        rules:{
            add_product : {
                product: '#multi-attributeBlock'
            }
        },
        messages: {
            add_product: {product: 'Please add product'},
        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmSetmeal').attr('action'),
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
                        window.location = adminUrl + '/setmeal';
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

function addProduct() {
    var inputCount = $('#product_count');
    var num = inputCount.val();


    var mod_value = $("#product_id").find("option:selected").val();
    var mod_text = $("#product_id").find("option:selected").text();

    var modifier = $('#product').val();
    var is_enabled = $('#is_enabled').val();

    var mod_price = $('#prod_qty').val();

    var no = $('#no').val();
    var yes = $('#yes').val();
    var existsFlag = true;
    if (mod_value == '') {
        toastr.error($('#select_prod_error').text());
        $('#product_exists_error').hide();
        return false;
    }
    $('.prod_id').each(function (k, v) {
        var text_value = $(v).val();

        if (text_value == mod_value) {
            existsFlag = false;
            toastr.error($('#product_exists_error').text());
            //$('#mod_exists_error').show();
            //$('#select_mod_error').hide();
            return false;
        }
    });
    if (existsFlag) {
        var html = '';
        $('#select_prod_error').hide();
        $('#product_exists_error').hide();
        var lang = $('#lang').val();
        var flt;

        flt = 'float-right';

        var attribute_html = '';

        html += '<div class="col-md-4 table-responsive" id="product_detail_div_' + num + '">';
        html += '<table class="table table-sm table-bordered mb-2">';
        html += '<tbody style="border-bottom: 2px solid #dee2e6;">';
        html += '<thead class="thead-light">';
        html += '<tr class="text-center">';
        html += '<th colspan="2">' + mod_text;
        html += '<a href="javascript:void(0);" class="text-dark"><i onclick="removeProduct(' + num + ')" class="fa fa-times float-right mt-1" aria-hidden="true"></i></a>';
        html += '<input type="hidden" class="prod_id" id="prod_id_' + num + '" value="' + mod_value + '" name="product_id[' + num + ']">';
        html += '<input type="hidden" id="selectedText_prod' + num + '" value="' + mod_text + '">';
        html += '</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tr>';
        html += '<td>';
        html += '<input type="number" name="prod_qty[' + num + ']" value="" id="prod_qty_' + num + '" class="form-control form-control-sm" placeholder="' + mod_price + '" required min="0">';
        html += '</td>';
        html += '</tr>';
        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        $('#multi-attributeBlock').append(html);
        inputCount.val(parseInt(num) + 1);

        $("#product_id").val('').change();
    }
}

function removeProduct(num) {
    var inputCount = $('#product_count');
    var num_count = inputCount.val();
	//$('#product_count').val(num_count - 1);
    $('#product_detail_div_' + num).remove();
}

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

function addSetmealAttribute() {
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

                attribute_html += '<tr>';
                attribute_html += '<td>' + response.data[i].name + '</td>';
                attribute_html += '<td>';
                attribute_html += '<input type="hidden" class="att_id" id="att_id_' + num + '" value="' + response.data[i].attribute_id + '" name="attribute_id_[' + att_value + '][]">';
                attribute_html += '<input type="number" name="att_price_' + response.data[i].attribute_id + '[' + att_value + '][]" value="" id="att_price_' + att_value + i + '" class="form-control form-control-sm att_price" placeholder="' + att_price + '" required min="0">';
                attribute_html += '</td>';
                attribute_html += '</tr>';

                j++;
            }
            html += '<div class="col-md-4 table-responsive" id="attribute_detail_div_' + num + '">';
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead class="thead-light">';
            html += '<tr class="text-center">';
            html += '<th colspan="2">' + att_text;
            html += '<a href="javascript:void(0);" class="text-dark"><i onclick="removeSetmealAtt(' + num + ')" class="fa fa-times float-right mt-1" aria-hidden="true"></i></a>';
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


            $('#multi-setattributeBlock').append(html);
            inputCount.val(parseInt(num) + 1);

            $("#att_category_id").val('').change();

        });
    }
}

function removeSetmealAtt(num) {
    var inputCount = $('#attribute_count');
    var num_count = inputCount.val();
    $('#attribute_detail_div_' + num).remove();
}

function deleteSetmeal(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/setmeal/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}