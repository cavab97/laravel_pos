@php
    $cartCounter = \App\Models\Helper::cartCounter();
    $userData = \Illuminate\Support\Facades\Auth::guard('fronts')->user();
@endphp
<header>
    <nav class="custom-navbar navbar navbar-expand-lg navbar-light bg-gray">
        <div class="container">
            <div class="custom-header">
                <div class="menu-logo-sec d-flex">
                    <button class="d-none menu-toggler border-0 navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false"
                            aria-label="Toggle navigation">

                        <div class="menu-btn">
                            <img src="{{asset('frontend/images/toggle-menu.png')}}"/>
                        </div>
                    </button>
                    <a class="header-logo navbar-brand" href="{{route('index')}}">
                        <img src="{{asset('frontend/images/logo.png')}}" class="mw-100">
                    </a>
                </div>

                <div class="menu-listing" style="display:none;">

                </div>
                <div class="header-right d-flex align-items-center">
					@if(Request::is('product/*'))
                    <div class="header-search-section d-md-block d-none">
                        <div class="header-search input-group">
                            <input type="text" class="custom-input-search form-control" id="keyword" onkeyup="pressEnter(event)"
                                   placeholder="Search products here...">
                            <div class="search-btn-sec input-group-append">
                                <button class="btn-search btn" type="button" id="searchProd" data-slug="@if(isset($branchSlug)){{$branchSlug}}@endif" onclick="searchProduct()">
                                    <i class="clr-red fa fa-search"></i>
                                </button>
                            </div>
                        </div>
						<input type="hidden" name="branchslug" id="branchslug" data-slug="@if(isset($branchSlug)){{$branchSlug}}@endif">
                        <input type="hidden" name="catslug" id="catslug" data-slug="@if(isset($category)){{$category}}@endif">
                    </div>
					@endif
                    <ul class="d-inline-flex  align-items-center">
                        @if(empty($userData))
                        <li class="nav-item">
                            <div class="header-signin">
                                <a href="javascript:void(0);" class="btn-login btn" onclick="loginPopup(this)">
                                                <span class="mr-2">
                                                    <i class="far fa-user"></i>
                                                </span> Login
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="header-signin">
                                <a href="javascript:void(0);" class="btn-register btn" onclick="signUp(this)">
                                                <span class="mr-2">
                                                    <i class="fas fa-lock"></i>
                                                </span> Register
                                </a>
                            </div>
                        </li>
                        @else
                            <li class="nav-item">
                                <div class="header-signin">
                                    <a href="{{route('logout')}}" class="btn-login btn">
                                                <span class="mr-2">
                                                    <i class="fa fa-sign-out"></i>
                                                </span> Logout
                                    </a>
                                </div>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            @if(!empty($userData))
                                <a class="header-icon" href="{{route('orderList',$branchSlug)}}">
                                            <span>
                                                <img src="{{asset('frontend/images/myorder.png')}}" style="height: 35px;"/>
                                            </span>
                                </a>
                            @endif

                            <a class="header-icon" href="{{route('cart.cartCheckOut',$branchSlug)}}">
                                        <span>
                                            <img src="{{asset('frontend/images/cart.png')}}" style="height: 35px;"/>
                                        </span>
                                <span class="custom-badge bg-red badge badge-danger navbar-badge rounded-circle"
                                      id="cart_counter">{{$cartCounter}}</span>
                            </a>

                            {{--<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <a href="{{route('cart.cartCheckOut',$branchSlug)}}" class="dropdown-item">
                                    <i class="fas fa-shopping-cart mr-2"></i> My Cart
                                </a>
                                @if(!empty($userData))
                                    <div class="dropdown-divider"></div>
                                    <a href="{{route('orderList',$branchSlug)}}" class="dropdown-item">
                                        <i class="fa fa-first-order mr-2"></i> My Orders
                                    </a>
                                @endif
                            </div>--}}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
