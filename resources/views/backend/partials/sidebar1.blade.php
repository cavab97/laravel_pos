@php
    $backend = config('constants.admin');
    $userData = \Illuminate\Support\Facades\Auth::user();
	$permission = new \App\Models\Permissions();
@endphp
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void(0);" class="brand-link">
        <img src="{{asset('backend/dist/img/logo.png')}}" alt="AdminLTE Logo"
             class="brand-image"
             style="float: none;">
        {{--<span class="brand-text font-weight-light">MCN</span>--}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
       <!--  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('backend/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2"
                     alt="{{$userData->name}}">
            </div>
            <div class="info">
                <a href="javascript:void(0);" class="d-block">{{$userData->name}}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link custom-user-panel-nav-link px-0">
                     <div class="custom-user-panel user-panel">
                            <div class="image">
                                <img src="{{asset('backend/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2"
                                     alt="{{$userData->name}}">
                            </div>
                            <div class="info">
                                <p>{{$userData->name}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>

                            </div>
                        </div>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Profile</p>
                        </a>
                      </li>
                    </ul>
                </li>
				@if($permission::checkActionPermission('view_dashboard'))
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend', '$backend/profile', '$backend/404', '$backend/403', '$backend/500')) ? 'active' : ''}}"
                       href="{{route('admin.home')}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{trans('backend/common.dashboard')}}</p>
                    </a>
                </li>
				@endif
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/roles','$backend/roles/*')) ? 'active' : ''}}"
                       href="{{route('admin.roles.index')}}">
                        <i class="nav-icon fas fa fa-tasks"></i>
                        <p>{{trans('backend/roles.roles')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/users','$backend/users/*')) ? 'active' : ''}}"
                       href="{{route('admin.users.index')}}">
                        <i class="nav-icon fas fa fa-users"></i>
                        <p>{{trans('backend/users.users')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/branch','$backend/branch/*')) ? 'active' : ''}}"
                       href="{{route('admin.branch.index')}}">
                        <i class="nav-icon fas fa fa-code-fork"></i>
                        <p>{{trans('backend/branch.branch')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/product','$backend/product/*')) ? 'active' : ''}}"
                       href="{{route('admin.product.index')}}">
                        <i class="nav-icon fas fa fa-product-hunt"></i>
                        <p>{{trans('backend/product.product')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/attributes','$backend/attributes/*')) ? 'active' : ''}}"
                       href="{{route('admin.attributes.index')}}">
                        <i class="nav-icon fas fa fa-align-center"></i>
                        <p>{{trans('backend/attributes.attributes')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/modifier','$backend/modifier/*')) ? 'active' : ''}}"
                       href="{{route('admin.modifier.index')}}">
                        <i class="nav-icon fas fa fa-history"></i>
                        <p>{{trans('backend/modifier.modifier')}}</p>
                    </a>
                </li>
				<li class="nav-item">
                                    <a class="nav-link {{(Request::is('$backend/product_inventory','$backend/product_inventory/*')) ? 'active' : ''}}"
                                       href="{{route('admin.product_inventory.index')}}">
                                        <i class="nav-icon fas fa fa-hourglass-half"></i>
                                        <p>{{trans('backend/common.inventory')}}</p>
                                    </a>
                                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/category','$backend/category/*')) ? 'active' : ''}}"
                       href="{{route('admin.category.index')}}">
                        <i class="nav-icon fas fa fa-list"></i>
                        <p>{{trans('backend/category.category')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/price_type','$backend/price_type/*')) ? 'active' : ''}}"
                       href="{{route('admin.price_type.index')}}">
                        <i class="nav-icon fas fa fa-product-hunt"></i>
                        <p>{{trans('backend/unit_type.price_type')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/printer','$backend/printer/*')) ? 'active' : ''}}"
                       href="{{route('admin.printer.index')}}">
                        <i class="nav-icon fas fa fa-print"></i>
                        <p>{{trans('backend/printer.printer')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/table','$backend/table/*')) ? 'active' : ''}}"
                       href="{{route('admin.table.index')}}">
                        <i class="nav-icon fas fa fa-table"></i>
                        <p>{{trans('backend/table.table')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/kitchen','$backend/kitchen/*')) ? 'active' : ''}}"
                       href="{{route('admin.kitchen.index')}}">
                        <i class="nav-icon fas fa fa-cutlery"></i>
                        <p>{{trans('backend/kitchen.kitchen')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/voucher','$backend/voucher/*')) ? 'active' : ''}}"
                       href="{{route('admin.voucher.index')}}">
                        <i class="nav-icon fas fa fa-gift"></i>
                        <p>{{trans('backend/voucher.voucher')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('$backend/terminal','$backend/terminal/*')) ? 'active' : ''}}"
                       href="{{route('admin.terminal.index')}}">
                        <i class="nav-icon fas fa fa-terminal"></i>
                        <p>{{trans('backend/terminal.terminal')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="{{route('admin.logout')}}">
                        <i class="fas fa-sign-out-alt nav-icon"
                           aria-hidden="true"></i>
                        <p>SignOut</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
