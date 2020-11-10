@php
    $lang = \App\Models\Languages::getBackLang();

@endphp
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel15">{{trans('backend/country.add_country')}}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        @include('backend.country.form')
    </div>
</div>
<script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('backend/dist/js/pages/country.js')}}"></script>