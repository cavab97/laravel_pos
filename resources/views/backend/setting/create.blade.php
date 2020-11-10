<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/setting.add_setting')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        @include('backend.setting.form')
    </div>
</div>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/setting.js')}}"></script>
<script>
    $(function () {
        $('input[name="value"]').rules('remove');


        if ($('#type').val() == 1) {  // String
            $('input[name="value"]').rules('add', {
                lettersonly: true,
                required: true
            });
        } else if ($('#type').val() == 2) {  // Integer
            $('input[name="value"]').rules('add', {
                digits: true,
                required: true,
                messages: {
                    digits: 'Please enter only integer'
                }
            });
        } else if ($('#type').val() == 3) {  // Integer
            $('input[name="value"]').rules('add', {
                float: true,
                required: true
            });
        } else if ($('#type').val() == 4) {  // Boolean
            $("#textBoxId").hide();
            $("#selectBoxId").show();
            /*$('input[name="value"]').rules('add', {
                check_value_boolean: true,
                required: true
            });*/
        } else if ($('#type').val() == 5) {  // Integer
            $('input[name="value"]').rules('add', {
                check_value_color: true,
                required: true
            });
        } else if ($('#type').val() == 6) {  // minutes
            $('input[name="value"]').attr("placeholder",'minutes');
            $('input[name="value"]').attr("type",'number');
            $('input[name="value"]').attr("min",'1');
            $('input[name="value"]').attr("max",'1440');
            $('input[name="value"]').rules('add', {
                //time24: true,
                required: true,
                max: 1440,
                messages:{
                    max: 'Please enter a value less than or equal to 24*60 minutes.'
                }
            });
        }

        $('input[name="value"]').valid();
    });
</script>