$(function () {
    $("#frmBranch").validate({
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
                check_closed_time : true
            },
        },
        messages:{

        },
        focusInvalid: true,
        submitHandler: function (form) {
            var $btn = $('#btnSubmit');
            $btn.html($btn.data('loading-text'));
            $btn.attr('disabled', true);
            $('.alert').hide();
            /* showHideLoader('show');*/
            $.ajax({
                url: $('#frmBranch').attr('action'),
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
                        window.location = adminUrl + '/branch';
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

    /*$.validator.addMethod("check_from_time", function(value, element) {

        var start = $("#open_from").val();
        var end = $("#closed_on").val();

        if((start.length > 0 && end.length > 0)){
            if(start>end) {
                return false;
            }
        }
        return true;

    }, "Please select a valid time");*/
    var message = 'End time always greater then start time.';
    $.validator.addMethod("check_closed_time", function(value, element) {

        var start = $("#open_from").val();
        var end = $("#closed_on").val();

        if((start.length > 0 && end.length > 0)){
            if (start > end) {
                return false;
            }
        }
        return true;


    }, message);
});

function onlyNumberKey(evt) {

    // Only ASCII charactar in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
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

function deleteBranch(e, id) {
    var modelId = $('#myModal');
    $.get(adminUrl + '/branch/' + id + '/delete', function (response) {
        modelId.html(response);
        modelId.modal({
            backdrop: 'static',
            keyboard: false
        });
    });
}

/*
function checkFromTime() {
    var from_time = $("#open_from").val();
    //var timeslot_minuts = $('#timeslot_minuts').val();
    var to_time = $('#closed_on').val();
    var date1, date2, hours, hourminut, minutes, total_minute;
    if (from_time != undefined && to_time != undefined && from_time != '' && to_time != '') {
        var st = minFromMidnight(from_time);
        var et = minFromMidnight(to_time);
        if (st > et) {
            $('#to_miss_error').show();
            $('#to_miss_error').text('Start time always less then then end time.');
            //alert('Start time always less then then end time.');
            return false;

        }else{
            $('#to_miss_error').hide();
            $('#to_miss_error').text('');
        }

    } else {
        if(from_time != '' && to_time != '')
        {
            $('#to_miss_error').show();
            $('#to_miss_error').text('Please select a valid time');
        }else{
            $('#to_miss_error').hide();
            $('#to_miss_error').text('');
        }
    }
}

function checkToTime() {
    var from_time = $("#open_from").val();
    //var timeslot_minuts = $('#timeslot_minuts').val();
    var to_time = $('#closed_on').val();

    if (from_time != undefined && to_time != undefined && from_time != '' && to_time != '') {
        var st = minFromMidnight(from_time);
        var et = minFromMidnight(to_time);
        if (st > et) {
            $('#to_miss_error').show();
            $('#to_miss_error').text('End time always greater then start time.');

            //alert('Start time always less then then end time.');
            return false;
        }else{
            $('#to_miss_error').hide();
            $('#to_miss_error').text('');
        }

    } else {
        if(from_time != '' && to_time != '')
        {
            $('#to_miss_error').show();
            $('#to_miss_error').text('Please select a valid time');
        }else{
            $('#to_miss_error').hide();
            $('#to_miss_error').text('');
        }
    }
}

function minFromMidnight(tm) {
    var ampm = tm.substr(-2);
    var clk;
    if (tm.length <= 6) {
        clk = tm.substr(0, 4);
    } else {
        clk = tm.substr(0, 5);
    }
    var m = parseInt(clk.match(/\d+$/)[0], 10);
    var h = parseInt(clk.match(/^\d+/)[0], 10);
    h += (ampm.match(/pm/i)) ? 12 : 0;
    return h * 60 + m;
}*/
