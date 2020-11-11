function checkCart(slug) {
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    if(slug != ''){
        $.ajax({
            url: baseUrl + '/branch/check-cart-product',
            method: 'post',
            data: {
                _token: csrf_token,
                branchSlug: slug,

            },
            success: function (json) {

                console.log(json);

                if (json.status == 422) {

                    var modelId = $('#myModal');
                    $.get(baseUrl + '/branch/cart-product-remove-popup/' + slug + '/' + json.message , function (response) {
                        modelId.html(response);
                        modelId.modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    });

                } else if(json.status == 500) {
                    toastr.error(json.message);
                } else if(json.status == 200) {
                    location.href = baseUrl + '/category/' + slug;
                }
            },
            error: function (error) {
                toastr.error('Oops....Something want wrong! Try again.');
            }
        });
    }
}

function clearCartItem(slug) {
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: baseUrl + '/branch/clear-cart-product',
        method: 'post',
        data: {
            _token: csrf_token,
            branchSlug: slug,
        },
        success: function (json) {

            if(json.status == 200) {
                location.href = baseUrl + '/category/' + slug;
            } else {
                toastr.error(json.message);
            }
        },
        error: function (error) {
            toastr.error('Oops....Something want wrong! Try again.');
        }
    });
}