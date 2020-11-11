//menu js
$(document).ready(function() {
    $(".btn-nav").on("click tap", function() {
        $(".nav-content").toggleClass("showNav hideNav").removeClass("hidden");
        $(".btn-nav").toggleClass("animated");
    });
});

function onlyNumberKey(evt) {

    // Only ASCII charactar in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}
