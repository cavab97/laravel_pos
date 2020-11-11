function loginPopup(e) {
    var modelId = $('#myModal');
    $.get(baseUrl + '/login', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function signUp(e) {
    var modelId = $('#myModal');
    $.get(baseUrl + '/signup', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

function forgotForm() {
    var modelId = $('#myModal');
    $.get(baseUrl + '/forgot-password', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}