function openProductPopup(e, uuid, branchslug,outofstock) {
    if(outofstock == 'true'){
        toastr.error('Product is out of stock.');
    } else {
        var modelId = $('#myModal');
        $.get(baseUrl + '/product/' + uuid + '/' + branchslug + '/popup', function (response) {
            modelId.html(response);
            modelId.modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    }
}


function incrementValue() {

    var currentVal = parseInt($('input[name="quantity"]').val(), 10);

    if (!isNaN(currentVal)) {
        currentVal  = currentVal + 1;
        $('input[name="quantity"]').val(currentVal);

        var product_price = parseFloat($("#prod_price").data('id'));
        var cart_total = 0;
        var total = 0;
        total = parseFloat(product_price * currentVal);
        var modifier = [];
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });

        /*modifier Calculation*/
        $.each($("input[name='prod_modifier[]']:checked"), function(){
            modifier.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });
        cart_total = total;
        $("#cart_total").text(cart_total);
    } else {
        $('input[name="quantity"]').val(0);
        var product_price = parseFloat($("#prod_price").data("id"));
        var cart_total = 0;
        var total = 0;
        total = parseFloat(product_price);
        var modifier = [];
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });

        /*modifier Calculation*/
        $.each($("input[name='prod_modifier[]']:checked"), function(){
            modifier.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });
        cart_total = total;
        $("#cart_total").text(cart_total);
    }
}

function decrementValue() {

    var currentVal = parseInt($('input[name="quantity"]').val(), 10);
    currentVal  = currentVal - 1;

    if (!isNaN(currentVal) && currentVal > 0) {

        $('input[name="quantity"]').val(currentVal);
        var product_price = parseFloat($("#prod_price").data("id"));
        var cart_total = 0;
        var total = 0;
        total = parseFloat(product_price * currentVal);
        var modifier = [];
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });

        /*modifier Calculation*/
        $.each($("input[name='prod_modifier[]']:checked"), function(){
            modifier.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });
        cart_total = total;
        $("#cart_total").text(cart_total);
    } else {
        $('input[name="quantity"]').val(0);
        var product_price = parseFloat($("#prod_price").data("id"));
        var cart_total = 0;
        var total = 0;
        total = product_price;
        var modifier = [];
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
            total += parseFloat($(this).data("price"));
        });

        /*modifier Calculation*/
        $.each($("input[name='prod_modifier[]']:checked"), function(){
            modifier.push($(this).val());
            total += parseFloat($(this).data("price"));
        });
        cart_total = total;
        $("#cart_total").text(cart_total);
    }
}

/*$(function () {
    var currentVal = $('input[name="quantity"]').val();
    var product_price = parseFloat($("#prod_price").data("id"));
    var cart_total = 0;
    var total = 0;
    total = parseFloat(product_price * currentVal);
    /!*attribute Calculation*!/
    $.each($("input[name='prod_attribute[]']:checked"), function(){
        total += (parseFloat($(this).data("price")) * currentVal);
    });
    /!*modifier Calculation*!/
    $.each($("input[name='prod_modifier[]']:checked"), function(){
        total += (parseFloat($(this).data("price")) * currentVal);
    });
    cart_total = total;
    $("#cart_total").text(cart_total);
});*/

$(document).on('change','input[name="prod_modifier[]"]',function() {

    var currentVal = $('input[name="quantity"]').val();
    var product_price = parseFloat($("#prod_price").data("id"));
    var cart_total = 0;
    var total = 0;
    total = parseFloat(product_price * currentVal);
    /*attribute Calculation*/
    $.each($("input[name='prod_attribute[]']:checked"), function(){
        total += (parseFloat($(this).data("price")) * currentVal);
    });

    /*modifier Calculation*/
    $.each($("input[name='prod_modifier[]']:checked"), function(){
        total += (parseFloat($(this).data("price")) * currentVal);
    });
    cart_total = total;
    $("#cart_total").text(cart_total);
});

$(document).on('change','input[name="prod_attribute[]"]',function() {

        var currentVal = $('input[name="quantity"]').val();
        var product_price = parseFloat($("#prod_price").data("id"));
        var cart_total = 0;
        var total = 0;
        total = parseFloat(product_price * currentVal);
        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            total += (parseFloat($(this).data("price")) * currentVal);
        });

        /*modifier Calculation*/
        $.each($("input[name='prod_modifier[]']:checked"), function(){
            total += (parseFloat($(this).data("price")) * currentVal);
        });
        cart_total = total;
        $("#cart_total").text(cart_total);

});

function addToCart() {
    $("#addToCart").attr('disabled',true);
    var branch_slug = $("#branch_slug").data("id");
    /*var table_uuid = $("#table_uuid").data("id");*/
    var quantity = $("#quantity").val();
    var product_id = $("#product_id").data("id");
    var modifier = [];
    var attribute = [];
    if($('input[name="prod_attribute[]"]').length){
        var checkedNum = $('input[name="prod_attribute[]"]:checked').length;
        if (!checkedNum) {
            toastr.error('Please pick a attribute');
            $("#addToCart").attr('disabled', false);
            return false;
        }
    }
    if(quantity > 0 && quantity != '') {
        /*attribute*/
        $.each($("input[name='prod_attribute[]']:checked"), function () {
            attribute.push($(this).val());
        });

        /*modifier*/
        $.each($("input[name='prod_modifier[]']:checked"), function () {
            modifier.push($(this).val());
        });
        var csrf_token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: baseUrl + '/addtocart',
            method: 'post',
            data: {
                _token: csrf_token,
                product_id: product_id,
                quantity: (typeof (quantity) != 'undefined' ? quantity : 1),
                modifier: modifier,
                attribute: attribute,
                branch_slug: branch_slug,
                is_setmeal: 0
            },
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (json) {

                console.log(json);
                var message = json.message;
                $("#addToCart").attr('disabled',false);
                if (json.status == 200) {
                    $("#cart_counter").html(json.cart_counter);
                    $("#myModal").modal('hide');
                    toastr.success(message);
                } else {
                    toastr.error(message);
                }
            }
        });
    } else {
        toastr.error('Please enter a quantity greater than 0');
        $("#addToCart").attr('disabled',false);
    }
}

$(document).ready(function () {
    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        $(".loading").show();
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        searchProduct(page);
    });
});

function searchProduct(page) {
    $(".se-pre-con").fadeIn("slow");
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    var search = $("#keyword").val();
    var branchSlug = $("#branchslug").data('slug');
    var catslug = $("#catslug").data('slug');
    $.ajax({
        url: baseUrl + '/product-listing?page=' + page,
        method: 'post',
        data: {
            _token: csrf_token,
            search: search,
            branchSlug: branchSlug,
            category: catslug,
        },
        success: function (res) {
            $('#myPostData').html(res);
            $(".se-pre-con").fadeOut("slow");
            //$('#loader').delay(100).fadeOut('slow');
        },
        error: function (err) {
            $(".se-pre-con").fadeOut("slow");
        }
    });
}

function pressEnter(event) {
    if (event.keyCode === 13 || event.type == 'change') {
        $("#searchProd").click();
    }
}

function addToCartProd(e,outofstock) {
    if(outofstock == 'true'){
        toastr.error('Product is out of stock.');
    } else {
        var id = $(e).data('id');
        $("#addToCart").attr('disabled', true);
        var branch_slug = $("#sim_branch_slug" + id).data("id");
        /*var table_uuid = $("#table_uuid").data("id");*/
        var quantity = $("#sim_quantity" + id).val();
        var product_id = $("#sim_product_id" + id).data("id");
        var modifier = [];
        var attribute = [];

        if (quantity > 0 && quantity != '') {

            var csrf_token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: baseUrl + '/addtocart',
                method: 'post',
                data: {
                    _token: csrf_token,
                    product_id: product_id,
                    quantity: (typeof (quantity) != 'undefined' ? quantity : 1),
                    modifier: modifier,
                    attribute: attribute,
                    branch_slug: branch_slug,
                    is_setmeal: 0
                },
                beforeSend: function () {

                },
                complete: function () {

                },
                success: function (json) {

                    console.log(json);
                    var message = json.message;
                    $("#addToCart").attr('disabled', false);
                    if (json.status == 200) {
                        $("#cart_counter").html(json.cart_counter);
                        toastr.success(message);
                    } else {
                        toastr.error(message);
                    }
                }
            });
        } else {
            toastr.error('Please enter a quantity greater than 0');
            $("#addToCart").attr('disabled', false);
        }
    }
}