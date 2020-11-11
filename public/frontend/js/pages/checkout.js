function updateCart(cart_id, cart_detail_id, product_id, num, slug) {

    var quantity = $('#quantity' + num).val();
    var voucher_id = $("#voucher_id").data('value');
    var cust_email = $('#cust_email').val();
    var cust_mobile = $('#cust_mobile').val();
    if (quantity != '') {
        $(".se-pre-con").fadeIn("slow");
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: baseUrl + '/cart/update',
            method: 'post',
            data: {
                _token: csrf_token,
                cart_id: cart_id,
                cart_detail_id: cart_detail_id,
                product_id: product_id,
                quantity: quantity,
                voucher_id: voucher_id,
                slug: slug,
                cust_email: cust_email,
                cust_mobile: cust_mobile

            },
            success: function (json) {
                $(".se-pre-con").fadeOut("slow");
                toastr.success('Cart updated successfully');
                $("#checkoutTable").html(json);
                $("#cart_counter").html($('.productcart').length);

            },
            error: function (error) {
                $(".se-pre-con").fadeOut("slow");
                toastr.error('Oops....Something want wrong! Try again.');
            }
        });
    } else {
        //$(".se-pre-con").fadeOut("slow");
        toastr.error('Please enter quantity!');
    }
}

function removeCartItemConfirm(cart_detail_id, slug) {
    var modelId = $('#myModal');
    $.get(baseUrl + '/cart/remove_confirm/' + cart_detail_id + '/' + slug, function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function removeCartItem(cart_detail_id, slug) {
    $("#myModal").modal('hide');
    $(".se-pre-con").fadeIn("slow");
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    var voucher_id = $("#voucher_id").data('value');
    var cust_email = $('#cust_email').val();
    var cust_mobile = $('#cust_mobile').val();
    $.ajax({
        url: baseUrl + '/cart/remove',
        method: 'post',
        data: {
            _token: csrf_token,
            cart_detail_id: cart_detail_id,
            slug: slug,
            voucher_id: voucher_id,
            cust_email: cust_email,
            cust_mobile: cust_mobile
        },
        success: function (json) {
            $(".se-pre-con").fadeOut("slow");
            console.log(json);
            toastr.success('Cart updated successfully');
            $("#checkoutTable").html(json);
            $("#cart_counter").html($('.productcart').length);
            /*if (json.status == 200) {
                localStorage.setItem('message',json.message);
                location.href = json.url;
            }else{

                toastr.error(json.message);
            }*/
        },
        error: function (error) {
            $(".se-pre-con").fadeOut("slow");
            toastr.error('Oops....Something want wrong! Try again.');
        }
    });
}

function checkVoucher(elem) {
    var voucher_code = $("#voucher_code").val();
    var branchSlug = $(elem).data("id");

    if (voucher_code == '') {
        // $("#err_msg_code").show();
        toastr.error('Please enter voucher code!');
    } else {
        $("#err_msg_code").hide();
        $(".se-pre-con").fadeIn("slow");

        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: baseUrl + '/cart/check-voucher',
            method: 'post',
            data: {
                _token: csrf_token,
                voucher_code: voucher_code,
                branchSlug: branchSlug
            },
            success: function (json) {
                $(".se-pre-con").fadeOut("slow");
                if (json.status == 200) {
                    var voucher_id = json.voucher_id;
                    var branchSlug = json.branchSlug;

                    $.ajax({
                        url: baseUrl + '/cart/apply-voucher',
                        method: 'post',
                        data: {
                            _token: csrf_token,
                            voucher_id: voucher_id,
                            branchSlug: branchSlug
                        },
                        success: function (res) {

                            if (res.status == 200) {
                                $("#voucher_code").val('');
                                $("#discount_tag").addClass('d-flex').show();
                                $("#discount_value").html(res.discount);
                                $("#voucher_amount").attr('data-value', res.discount);
                                if (res.branchTax.length > 0) {
                                    var html = '';
                                    for (var i = 0; i < res.branchTax.length; i++) {
                                        html += '<div class="product-price-row row justify-content-end align-items-center mb-2 mx-auto">';
                                        html += '<div class="col-lg-2 col-md-3 col-5">';
                                        html += '<div class="price-header">';
                                        html += '<span>Tax(' + res.branchTax[i].code + ' ' + res.branchTax[i].rate + '%)</span>';
                                        html += '</div>';
                                        html += '</div>';
                                        html += '<div class="col-lg-1 col-md-2 col-4">';
                                        html += '<div class="product-light-header">';
                                        html += '<span>MYR ' + parseFloat(res.branchTax[i].taxAmount).toFixed(2) + '</span>';
                                        html += '</div>';
                                        html += '</div>';
                                        html += '</div>';
                                        /*html += '<td></td>';
                                        html += '<td></td>';
                                        html += '<td></td>';
                                        html += '<td></td>';
                                        html += '<th>Tax(' + res.branchTax[i].code + ' ' + res.branchTax[i].rate + '%)</th>';
                                        html += '<td>MYR ' + parseFloat(res.branchTax[i].taxAmount).toFixed(2) + '</td>';*/

                                    }
                                    $("#tax").html(html);
                                }
                                $("#voucher_id").attr('data-value', res.voucher_id);
                                $("#total").html(parseFloat(res.amount).toFixed(2));

                            } else {

                                toastr.error(res.message);
                            }
                        },
                        error: function (error) {

                            toastr.error('Oops....Something want wrong! Try again.');
                        }
                    });

                } else {

                    toastr.error(json.message);
                }
            },
            error: function (error) {
                $(".se-pre-con").fadeOut("slow");
                toastr.error('Oops....Something want wrong! Try again.');
            }
        });
    }
}

function paymentOption(elem) {
    $("#email_err").hide();
    $("#cust_email").removeClass('is-invalid');
    var branchSlug = $(elem).data("id");
    $(elem).html($(elem).data('loading-text'));
    $(elem).attr('disabled', true);
    var mobile = $("#cust_mobile").val();
    var email = $("#cust_email").val();
    if(email != ""){
        var inputVal = $("#cust_email").val();
        var emailReg = /^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$/;
        if(!emailReg.test(inputVal)) {
            var element = document.getElementById("cust_email");
            element.scrollIntoView();
            element.scrollIntoView(false);
            element.scrollIntoView({block: "end"});
            element.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
            $("#cust_email").addClass('is-invalid');
            $("#email_err").show();
            $(elem).attr('disabled', false);
            $(elem).html($(elem).data('original-text'));
            return false;
        } else {
            $("#cust_email").removeClass('is-invalid');
            $("#email_err").hide();
        }
    }
    if (mobile == '') {
        toastr.error('Please enter mobile number!');
        $(elem).attr('disabled', false);
        $(elem).html($(elem).data('original-text'));
    } else {
        var modelId = $('#myModal');
        $.get(baseUrl + '/cart/payment-option/' + branchSlug + '/' + mobile + '/' + email, function (response) {
            modelId.html(response);
            $(elem).attr('disabled', false);
            $(elem).html($(elem).data('original-text'));
            modelId.modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    }

}

function createOrder(elem, slug, mobile, email) {
    $("#loader").fadeIn(1000);
    $(".list-group-item").addClass("disabled");
    var paymentId = $(elem).data("id");
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    var voucher_id = $("#voucher_id").data('value');
    var voucher_amount = $("#voucher_amount").data('value');
    $.ajax({
        url: baseUrl + '/cart/create-order',
        method: 'post',
        data: {
            _token: csrf_token,
            branchSlug: slug,
            payment_id: paymentId,
            voucher_id: voucher_id,
            voucher_amount: voucher_amount,
            mobile: mobile,
            email: email
        },
        success: function (json) {

            console.log(json);

            if (json.status == 200) {
                localStorage.setItem('message', json.message);
                location.href = baseUrl + '/order-success/' + json.order_number + '/' + json.uuid;
            } else {
                $("#loader").fadeOut(1000);
                toastr.error(json.message);
                $(".list-group-item").removeClass("disabled");
                $("#livePayment").addClass("disabled");
            }
        },
        error: function (error) {
            $("#loader").fadeOut(1000);
            toastr.error('Oops....Something want wrong! Try again.');
            $(".list-group-item").removeClass("disabled");
            $("#livePayment").addClass("disabled");
        }
    });
}

$('#cust_email').keyup(function() {
    $("#email_err").hide();
    $("#cust_email").removeClass('is-invalid');
    var inputVal = $(this).val();
    var emailReg = /^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$/;
    if(inputVal != '') {
        if (!emailReg.test(inputVal)) {
            $("#cust_email").addClass('is-invalid');
            $("#email_err").show();
        }
    } else {
        $("#cust_email").removeClass('is-invalid');
        $("#email_err").hide();
    }
});

$('input[name^="quantity["]').on('input', function(){
    if ($(this).val())
    {
        var id = $(this).data('id');
        $("#btnUpdate_" + id).show();
        $("#btnTrash_" + id).hide();
        //$("#btnUpdate_" + id).click();
    }
});

$('input[name^="quantity["]').on('focusout', function(){
    if ($(this).val())
    {
        var id = $(this).data('id');
        $("#btnUpdate_" + id).hide();
        $("#btnTrash_" + id).hide();
        $("#btnUpdate_" + id).click();
    }
});

$(function() {

    $('.list-group-item').on('click', function() {
        $('.fa', this)
            .toggleClass('fa-chevron-right')
            .toggleClass('fa-chevron-down');
    });

});

function pressEnter(event) {
    if (event.keyCode === 13 || event.type == 'change') {
        $("#btnApply").click();
    }
}