<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/table_color.edit_table_color')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        @include('backend.table_color.form')
    </div>
</div>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/table-color.js')}}"></script>
<script src="{{asset('backend/dist/js/colorPick.js')}}"></script>
<script>
    /*$(function () {
        $("#color_code").colorPick({
            'initialColor': '',
            'allowRecent': true,
            'recentMax': 5,
            'allowCustomColor': true,
            'palette': ["#AFADAC", "#CD5C5C", "#F08080", "#FA8072", "#E9967A", "#FFA07A", "#DC143C", "#FF0000", "#FFC0CB", "#FFB6C1", "#FF69B4", "#FF1493", "#C71585", "#DB7093", "#FFFFE0", "#FFFACD", "#FAFAD2", "#BDB76B", "#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad", "#34495e", "#2c3e50", "#f1c40f", "#f39c12", "#e67e22", "#d35400", "#e74c3c", "#c0392b", "#ecf0f1", "#E6B0AA", "#D7BDE2", "#D2B4DE", "#AED6F1", "#F9E79F", "#FAD7A0", "#AEB6BF", "#ABB2B9", "#808B96", "#5D6D7E", "#99A3A4", "#A52A2A", "#DCDCDC", "#D3D3D3", "#C0C0C0", "#A9A9A9", "#808080", "#696969", "#778899", "#708090", "#2F4F4F", "#000000"],
            'onColorSelected': function () {
                console.log("The user has selected the color: " + this.color)
                $('#color_code').val(this.color);
                this.element.css({'backgroundColor': this.color, 'color': this.color});
            }
        });
    });*/
</script>