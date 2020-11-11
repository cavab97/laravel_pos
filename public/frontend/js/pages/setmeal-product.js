function openSetmealPopup(e, uuid, branchslug) {
    var modelId = $('#myModal');
    $.get(baseUrl + '/setmeal-product/' + uuid + '/' + branchslug +'/popup', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
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

        cart_total = total;
        $("#cart_total").text(cart_total);
    } else {
        $('input[name="quantity"]').val(0);
        var product_price = parseFloat($("#prod_price").data("id"));
        var cart_total = 0;
        var total = 0;
        total = parseFloat(product_price);

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
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
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
        var attribute = [];

        /*attribute Calculation*/
        $.each($("input[name='prod_attribute[]']:checked"), function(){
            attribute.push($(this).val());
            total += (parseFloat($(this).data("price")) * currentVal);
        });

        cart_total = total;
        $("#cart_total").text(cart_total);
    }
}

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

    cart_total = total;
    $("#cart_total").text(cart_total);

});

function addToCart() {
    $("#addToCart").attr('disabled',true);
    var branch_slug = $("#branch_slug").data("id");
    /*var table_uuid = $("#table_uuid").data("id");*/
    var quantity = $("#quantity").val();
    var setmeal_id = $("#setmeal_id").data("id");
    var product = [];
    var attribute = [];
    var i=0;
    if(quantity > 0 && quantity != '') {

        /*product*/
        $.each($("input[name='prod_setmeal[]']:checked"), function () {
            product.push($(this).val());
        });

        /*attribute*/
        $.each($("input[name='prod_attribute[]']:checked"), function () {
            attribute.push($(this).val());
        });

        var csrf_token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: baseUrl + '/addtocart',
            method: 'post',
            data: {
                _token: csrf_token,
                setmeal_id: setmeal_id,
                quantity: (typeof (quantity) != 'undefined' ? quantity : 1),
                product: product,
                attribute: attribute,
                branch_slug: branch_slug,
                is_setmeal: 1
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