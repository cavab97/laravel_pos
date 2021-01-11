@php
    $backend = config('constants.admin');
    $userData = \Illuminate\Support\Facades\Auth::user();
    $permission = new \App\Models\Permissions();

    function getPath($route) {
        return config('constants.admin').explode(config('constants.admin'), route($route))[1];
    }
    function isRequest($route) {
        return Request::is(getPath($route), getPath($route).'/*');
    }
@endphp
<style>
ul.nav > li.nav-item{
    max-width: 100%;
}
</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void(0);" class="brand-link">
        <img src="{{asset('backend/dist/img/logo.png')}}" alt="AdminLTE Logo" class="brand-image" style="float: none;">
        {{--<span class="brand-text font-weight-light">MCN</span>--}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
    {{--<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        <img src="{{asset('backend/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2"
    alt="{{$userData->name}}">
</div>
<div class="info">
    <a href="javascript:void(0);" class="d-block">{{$userData->name}}</a>
</div>
</div>--}}

    <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                <li class="nav-item has-treeview {{ isRequest('admin.profile') ? 'menu-open' : ''}}">
                    <a href="#" class="nav-link custom-user-panel-nav-link px-0">
                        <div class="custom-user-panel user-panel">
                            <div class="image">
                                <img src="{{asset('backend/dist/img/user2-160x160.jpg')}}"
                                     class="img-circle elevation-2" alt="{{$userData->name}}">
                            </div>
                            <div class="info">
                                <p>{{$userData->name}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>

                            </div>
                        </div>
                    </a>
                    <ul class="nav nav-treeview {{ isRequest('admin.profile') ? '' : 'display_none'}}">
                        <li class="nav-item">
                            <a href="{{route('admin.profile')}}"
                               class="nav-link {{ isRequest('admin.profile') ? 'active' : ''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{trans('backend/common.profile')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @if($permission::checkActionPermission('view_dashboard'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is(getPath('admin.home')) ? 'active' : ''}}"
                           href="{{route('admin.home')}}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>{{trans('backend/common.dashboard')}}</p>
                        </a>
                    </li>
                @endif

                @if($permission::checkActionPermission(['view_users','view_roles','view_branch']))
                    <li class="nav-item has-treeview {{
                            (   isRequest('admin.roles.index')
                            ||  isRequest('admin.users.index')
                            ||  isRequest('admin.branch.index')
                            ) ? 'menu-open' : ''}}">
                        <a class="nav-link {{
                            (   isRequest('admin.roles.index')
                            ||  isRequest('admin.users.index')
                            ||  isRequest('admin.branch.index')
                            )  ? 'active' : ''}}">
                            <i class="nav-icon fas fa-database"></i>
                            <p> {{trans('backend/common.user_management')}}</p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($permission::checkActionPermission('view_roles'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.roles.index') ? 'active' : ''}}"
                                       href="{{route('admin.roles.index')}}">
                                        <i class="nav-icon fas fa fa-tasks"></i>
                                        <p>{{trans('backend/roles.roles')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_users'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.users.index') ? 'active' : ''}}"
                                       href="{{route('admin.users.index')}}">
                                        <i class="nav-icon fas fa fa-users"></i>
                                        <p>{{trans('backend/users.users')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_branch'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.branch.index') ? 'active' : ''}}"
                                       href="{{route('admin.branch.index')}}">
                                        <i class="nav-icon fas fa fa-code-fork"></i>
                                        <p>{{trans('backend/branch.branch')}}</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if($permission::checkActionPermission(['view_product','view_category_attribute','view_attributes','view_modifier','view_product_inventory','view_setmeal']))
                    <li class="nav-item has-treeview {{
                            (   isRequest('admin.product.index')
                            ||  isRequest('admin.category.index')
                            ||  isRequest('admin.category_attribute.index')
                            ||  isRequest('admin.attributes.index')
                            ||  isRequest('admin.modifier.index')
                            ||  isRequest('admin.product_inventory.index')
                            ||  isRequest('admin.setmeal.index')
                            ||  isRequest('admin.price_type.index')
                            )   ? 'menu-open' : ''}}">
                        <a class="nav-link {{
                            (   isRequest('admin.product.index')
                            ||  isRequest('admin.category.index')
                            ||  isRequest('admin.category_attribute.index')
                            ||  isRequest('admin.attributes.index')
                            ||  isRequest('admin.modifier.index')
                            ||  isRequest('admin.product_inventory.index')
                            ||  isRequest('admin.setmeal.index')
                            ||  isRequest('admin.price_type.index')
                            )    ? 'active' : ''}}">
                            <i class="nav-icon fas fa-clipboard"></i>
                            <p> {{trans('backend/common.product_management')}}</p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($permission::checkActionPermission('view_product'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.product.index') ? 'active' : ''}}"
                                       href="{{route('admin.product.index')}}">
                                        <i class="nav-icon fas fa fa-product-hunt"></i>
                                        <p>{{trans('backend/product.product')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_category'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.category.index') ? 'active' : ''}}"
                                       href="{{route('admin.category.index')}}">
                                        <i class="nav-icon fas fa fa-list"></i>
                                        <p>{{trans('backend/category.category')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_category_attribute'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.category_attribute.index') ? 'active' : ''}}"
                                       href="{{route('admin.category_attribute.index')}}">
                                        <i class="nav-icon fas fa fa-align-justify"></i>
                                        <p>{{trans('backend/attributes.category_attribute')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_attributes'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.attributes.index') ? 'active' : ''}}"
                                       href="{{route('admin.attributes.index')}}">
                                        <i class="nav-icon fas fa fa-align-center"></i>
                                        <p>{{trans('backend/attributes.attributes')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_modifier'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.modifier.index') ? 'active' : ''}}"
                                       href="{{route('admin.modifier.index')}}">
                                        <i class="nav-icon fas fa fa-history"></i>
                                        <p>{{trans('backend/modifier.modifier')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_product_inventory'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.product_inventory.index') ? 'active' : ''}}"
                                       href="{{route('admin.product_inventory.index')}}">
                                        <i class="nav-icon fas fa fa-hourglass-half"></i>
                                        <p>{{trans('backend/common.inventory')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_setmeal'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.setmeal.index') ? 'active' : ''}}"
                                       href="{{route('admin.setmeal.index')}}">
                                        <i class="nav-icon fas fa fa-inbox"></i>
                                        <p>{{trans('backend/common.setmeal')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_price_type'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.price_type.index') ? 'active' : ''}}"
                                       href="{{route('admin.price_type.index')}}">
                                        <i class="nav-icon fas fa fa-product-hunt"></i>
                                        <p>{{trans('backend/unit_type.unit_type')}}</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if($permission::checkActionPermission('view_customer'))
                    <li class="nav-item">
                        <a class="nav-link {{(Request::is($backend.'/customer',$backend.'/customer/*')) ? 'active' : ''}}"
                           href="{{route('admin.customer.index')}}">
                            <i class="nav-icon fas fa fa-handshake-o"></i>
                            <p>{{trans('backend/customer.customers')}}</p>
                        </a>
                    </li>
                @endif
                @if($permission::checkActionPermission('view_order'))
                    <li class="nav-item">
                        <a class="nav-link {{(Request::is($backend.'/order',$backend.'/order/*')) ? 'active' : ''}}"
                           href="{{route('admin.order.index')}}">
                            <i class="nav-icon fas fa fa-shopping-cart"></i>
                            <p>{{trans('backend/order.transactions')}}</p>
                        </a>
                    </li>
                @endif

                @if($permission::checkActionPermission('view_setting'))
                    <li class="nav-item">
                        <a class="nav-link {{(Request::is($backend.'/setting',$backend.'/setting/*')) ? 'active' : ''}}"
                           href="{{route('admin.setting.index')}}">
                            <i class="nav-icon fas fa fa-cogs"></i>
                            <p>{{trans('backend/setting.setting')}}</p>
                        </a>
                    </li>
                @endif
                @if($permission::checkActionPermission(['view_price_type','view_printer','view_table','view_table_color','view_kitchen','view_voucher','view_terminal','view_banner','view_tax','view_logs','view_payment_type','view_country','view_states','view_cities']))
                    <li class="nav-item has-treeview {{
                            (   isRequest('admin.printer.index')
                            ||  isRequest('admin.table.index')
                            ||  isRequest('admin.table-color.index')
                            ||  isRequest('admin.kitchen.index')
                            ||  isRequest('admin.voucher.index')
                            ||  isRequest('admin.terminal.index')
                            ||  isRequest('admin.banner.index')
                            ||  isRequest('admin.tax.index')
                            ||  isRequest('admin.payment-type.index')
                            ||  isRequest('admin.logs.index')
                            ||  isRequest('admin.logs.pos-logs')
                            )  ? 'menu-open' : ''}}">

                        <a class="nav-link {{
                            (   isRequest('admin.printer.index')
                            ||  isRequest('admin.table.index')
                            ||  isRequest('admin.table-color.index')
                            ||  isRequest('admin.kitchen.index')
                            ||  isRequest('admin.voucher.index')
                            ||  isRequest('admin.terminal.index')
                            ||  isRequest('admin.banner.index')
                            ||  isRequest('admin.tax.index')
                            ||  isRequest('admin.payment-type.index')
                            ||  isRequest('admin.logs.index')
                            ||  isRequest('admin.logs.pos-logs')
                            )   ? 'active' : ''}}">
                            <i class="nav-icon fas fa-clipboard"></i>
                            <p> {{trans('backend/common.master')}}</p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($permission::checkActionPermission('view_printer'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.printer.index') ? 'active' : ''}}"
                                       href="{{route('admin.printer.index')}}">
                                        <i class="nav-icon fas fa fa-print"></i>
                                        <p>{{trans('backend/printer.printer')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_table'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.table.index') ? 'active' : ''}}"
                                       href="{{route('admin.table.index')}}">
                                        <i class="nav-icon fas fa fa-table"></i>
                                        <p>{{trans('backend/table.table')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_table_color'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.table-color.index') ? 'active' : ''}}"
                                       href="{{route('admin.table-color.index')}}">
                                        <i class="nav-icon fas fa fa-table"></i>
                                        <p>{{trans('backend/table_color.table_color')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_kitchen'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.kitchen.index') ? 'active' : ''}}"
                                       href="{{route('admin.kitchen.index')}}">
                                        <i class="nav-icon fas fa fa-cutlery"></i>
                                        <p>{{trans('backend/kitchen.kitchen')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_voucher'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.voucher.index') ? 'active' : ''}}"
                                       href="{{route('admin.voucher.index')}}">
                                        <i class="nav-icon fas fa fa-gift"></i>
                                        <p>{{trans('backend/voucher.voucher')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_terminal'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.terminal.index') ? 'active' : ''}}"
                                       href="{{route('admin.terminal.index')}}">
                                        <i class="nav-icon fas fa fa-terminal"></i>
                                        <p>{{trans('backend/terminal.terminal')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_banner'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.banner.index') ? 'active' : ''}}"
                                       href="{{route('admin.banner.index')}}">
                                        <i class="nav-icon fas fa fa-picture-o"></i>
                                        <p>{{trans('backend/banner.banner')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_tax'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.tax.index') ? 'active' : ''}}"
                                       href="{{route('admin.tax.index')}}">
                                        <i class="nav-icon fas fa fa-money-bill"></i>
                                        <p>{{trans('backend/tax.tax')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_payment_type'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.payment-type.index') ? 'active' : ''}}"
                                       href="{{route('admin.payment-type.index')}}">
                                        <i class="nav-icon fas fa fa-paypal"></i>
                                        <p>{{trans('backend/payment.payment_type')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_logs'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.logs.index') ? 'active' : ''}}"
                                       href="{{route('admin.logs.index')}}">
                                        <i class="nav-icon fas fa fa-history"></i>
                                        <p>{{trans('backend/logs.logs')}}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.logs.pos-logs') ? 'active' : ''}}"
                                       href="{{route('admin.logs.pos-logs')}}">
                                        <i class="nav-icon fas fa fa-history"></i>
                                        <p>{{trans('backend/logs.pos_logs')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission(['view_country','view_states','view_cities']))
                                <li class="nav-item has-treeview  {{
                            (   isRequest('admin.city.index')
                            ||  isRequest('admin.state.index')
                            ||  isRequest('admin.country.index')
                            )  ? 'menu-open' : ''}}">
                                    <a class="nav-link {{
                            (   isRequest('admin.city.index')
                            ||  isRequest('admin.state.index')
                            ||  isRequest('admin.country.index')
                            )  ? 'active' : ''}}">
                                        <i class="nav-icon fa fa-map-marker"></i>
                                        <p> {{trans('backend/common.location')}}</p>
                                        <i class="right fas fa-angle-left"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @if($permission::checkActionPermission('view_cities'))
                                            <li class="nav-item">
                                                <a class="nav-link {{ isRequest('admin.country.index') ? 'active' : ''}}"
                                                   href="{{ route('admin.city.index') }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{trans('backend/common.city')}}</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if($permission::checkActionPermission('view_states'))
                                            <li class="nav-item">
                                                <a class="nav-link {{ isRequest('admin.country.index') ? 'active' : ''}}"
                                                   href="{{ route('admin.state.index') }}"><i
                                                            class="far fa-circle nav-icon"></i>
                                                    <p>{{trans('backend/common.state')}}</p>
                                                </a>
                                            </li>
                                        @endif
                                        @if($permission::checkActionPermission('view_countries'))
                                            <li class="nav-item">
                                                <a class="nav-link {{ isRequest('admin.country.index') ? 'active' : ''}}"
                                                   href="{{ route('admin.country.index') }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{trans('backend/common.country')}}</p>
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if($permission::checkActionPermission(['view_rac','view_box']))
                    <li class="nav-item has-treeview {{
                            (   isRequest('admin.wine_store_management.index')
                            ||  isRequest('admin.rac.index')
                            ||  isRequest('admin.box.index')
                            ) ? 'menu-open' : ''}}">

                        <a class="nav-link {{
                            (   isRequest('admin.wine_store_management.index')
                            ||  isRequest('admin.rac.index')
                            ||  isRequest('admin.box.index')
                            )  ? 'active' : ''}}">
                            <i class="nav-icon fas fa-th"></i>
                            <p> {{trans('backend/rac.rac_management')}}</p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($permission::checkActionPermission('view_wine_store_management'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.wine_store_management.index') ? 'active' : ''}}"
                                       href="{{route('admin.wine_store_management.index')}}">
                                        <i class="nav-icon fas fa fa-beer"></i>
                                        <p>{{trans('backend/wine_store_management.wine_store_management')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_rac'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.rac.index') ? 'active' : ''}}"
                                       href="{{route('admin.rac.index')}}">
                                        <i class="nav-icon fas fa fa-th-large"></i>
                                        <p>{{trans('backend/rac.rac')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_box'))
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.box.index') ? 'active' : ''}}"
                                       href="{{route('admin.box.index')}}">
                                        <i class="nav-icon fas fa fa-square"></i>
                                        <p>{{trans('backend/rac.box')}}</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>
                @endif
                @if($permission::checkActionPermission('view_attendance'))
                    <li class="nav-item">
                        <a class="nav-link {{ isRequest('admin.attendance.index') ? 'active' : ''}}"
                           href="{{route('admin.attendance.index')}}">
                            <i class="nav-icon fas fa fa-clock-o"></i>
                            <p>{{trans('backend/attendance.attendance')}}</p>
                        </a>
                    </li>
                @endif
                @if($permission::checkActionPermission(['view_customer_reports','view_category_reports','view_shift_reports']))
                    <li class="nav-item has-treeview {{
                            (   isRequest('admin.reports.customer.index')
                            ||  isRequest('admin.reports.categroy_report.index')
                            ||  isRequest('admin.reports.shift_report.index')
                            ||  isRequest('admin.reports.cancelled_reports.index')
                            ||  isRequest('admin.reports.payment_transaction.index')
                            ) ? 'menu-open' : ''}}">
                        <a class="nav-link {{
                            (   isRequest('admin.reports.customer.index')
                            ||  isRequest('admin.reports.categroy_report.index')
                            ||  isRequest('admin.reports.shift_report.index')
                            ||  isRequest('admin.reports.cancelled_reports.index')
                            ||  isRequest('admin.reports.payment_transaction.index')
                            ) ? 'active' : ''}}">
                            <i class="nav-icon fas fa-database"></i>
                            <p> {{trans('backend/common.reports')}}</p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($permission::checkActionPermission('view_category_reports'))
                                <li class="nav-item">
                                    <a class="nav-link {{isRequest('admin.reports.categroy_report.index') ? 'active' : ''}}"
                                       href="{{route('admin.reports.categroy_report.index')}}">
                                        <i class="nav-icon fas fa fa-tasks"></i>
                                        <p>{{trans('backend/common.category_reports')}}</p>
                                    </a>
                                </li>
                            @endif
                            @if($permission::checkActionPermission('view_shift_reports'))
                                <li class="nav-item">
                                    <a class="nav-link {{isRequest('admin.reports.shift_report.index') ? 'active' : ''}}"
                                       href="{{route('admin.reports.shift_report.index')}}">
                                        <i class="nav-icon fas fa fa-user-circle"></i>
                                    <p>{{trans('backend/common.shift_reports')}}</p>
                                    </a>
                                </li>
                            @endif
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.reports.cancelled_reports.index') ? 'active' : ''}}"
                                       href="{{route('admin.reports.cancelled_reports.index')}}">
                                        <i class="nav-icon fas fa fa-user-circle"></i>
                                    <p>{{trans('backend/common.cancelled_reports')}}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ isRequest('admin.reports.payment_transaction.index') ? 'active' : ''}}"
                                       href="{{route('admin.reports.payment_transaction.index')}}">
                                        <i class="nav-icon fas fa fa-user-circle"></i>
                                    <p>{{trans('backend/common.payment_transaction')}}</p>
                                    </a>
                                </li>
                            {{--@if($permission::checkActionPermission('view_customer_reports'))
                                <li class="nav-item">
                                    <a class="nav-link {{isRequest('admin.reports.customer.index') ? 'active' : ''}}"
                                       href="{{route('admin.reports.customer.index')}}">
                                        <i class="nav-icon fas fa fa-tasks"></i>
                                        <p>{{trans('backend/common.customer_reports')}}</p>
                                    </a>
                                </li>
                            @endif--}}
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.logout')}}">
                        <i class="fas fa-sign-out-alt nav-icon" aria-hidden="true"></i>
                        <p>Sign Out</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
