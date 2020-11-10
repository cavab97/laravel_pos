@extends('backend.layout')
@section('scripts')
    <script src="{{asset('backend/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/pages/product.js')}}"></script>

@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="content-header-row row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{trans('backend/product.product')}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.home')}}">{{trans('backend/common.home')}}</a></li>
                            <li class="breadcrumb-item"><a
                                        href="{{route('admin.product.index')}}">{{trans('backend/product.product')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{trans('backend/common.view')}}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="custom-content content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('backend/product.view_product')}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                {{trans('backend/common.details')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.name')}}</strong>:
                                                        <span>{{$productData->product_name}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/common.name2')}}</strong>:
                                                        <span>@if(!empty($productData->product_name2))
                                                                {{$productData->product_name2}}
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/product.sku')}}</strong>: {{$productData->sku}}
                                                    </p>
                                                    <p><strong>{{trans('backend/product.price')}}</strong>:
                                                        @if($productData->old_price)
                                                            <del class="text-info">{{number_format($productData->old_price,2)}}</del>
                                                            {{number_format($productData->price,2)}}
                                                        @else
                                                            {{number_format($productData->price,2)}}
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/product.price_type')}}</strong>:
                                                        <span>{{$productData->price_type_name}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/product.has_inventory')}}</strong>:
                                                        @if($productData->has_inventory == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.no')}}</span>
                                                        @endif
                                                    </p>

                                                </div>


                                                <div class="col-md-6">
                                                    <p><strong>{{trans('backend/product.category')}}</strong>:
                                                        @foreach($productData->category as $key => $value)
                                                            <span class="badge badge-secondary">{{$value['category_name']}}</span>
                                                        @endforeach
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/category.has_rac_managemant')}}</strong>:
                                                        @if($productData->has_rac_managemant == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.no')}}</span>
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/product.has_setmeal')}}</strong>:
                                                        @if($productData->has_setmeal == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.no')}}</span>
                                                        @endif
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/product.price_type_value')}}</strong>:
                                                        <span>{{$productData->price_type_value}}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{trans('backend/common.status')}}</strong>:
                                                        @if($productData->status == 1)
                                                            <span class="badge badge-success">{{trans('backend/common.active')}}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{trans('backend/common.inactive')}}</span>
                                                        @endif
                                                    </p>

                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_by')}}</strong>:
                                                        <span>{{$productData->updated_name}}</span>
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    <p>
                                                        <strong>{{trans('backend/common.updated_at')}}</strong>:
                                                        <span>@if($productData->updated_at){{date('d-m-Y H:i:s', strtotime($productData->updated_at))}}@endif</span>
                                                    </p>
                                                </div>

                                                <div class="col-md-12">
                                                    <p><strong>{{trans('backend/common.description')}}</strong></p>
                                                    <span id="description_data">{!!($productData->product_description)!!}</span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">{{trans('backend/common.images')}}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div id="carouselExampleIndicators" class="carousel slide"
                                                 data-ride="carousel">
                                                <ol class="carousel-indicators">
                                                    @foreach ($productData->productImagesData as $key => $value)
                                                        <li data-target="#carouselExampleIndicators"
                                                            data-slide-to="{{$key}}"
                                                            class="{{($key == '0') ? 'active':''}}"></li>
                                                        <?php $key++?>
                                                    @endforeach
                                                </ol>
                                                <div class="carousel-inner">
                                                    @if(count($productData->productImagesData) > 0)
                                                        @foreach ($productData->productImagesData as $key => $value)
                                                            <div class="carousel-item {{($key == '0') ? 'active':''}}">
                                                                @if(!empty($value['asset_path']) && public_path($value['asset_path']))
                                                                    <img class="d-block w-100" style="height: 345px"
                                                                         src="{{asset($value['asset_path'])}}">
                                                                @else
                                                                    <img class="d-block w-100" style="height: 345px"
                                                                         src="{{asset(config('constants.default_product'))}}">
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <img class="d-block w-100" style="height: 320px"
                                                             src="{{asset(config('constants.default_product'))}}">
                                                    @endif
                                                </div>
                                                <a class="carousel-control-prev" href="#carouselExampleIndicators"
                                                   role="button" data-slide="prev">
                                                        <span class="carousel-control-prev-icon"
                                                              aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carouselExampleIndicators"
                                                   role="button" data-slide="next">
                                                        <span class="carousel-control-next-icon"
                                                              aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if(count($productData->modifier)>0)
                                    <div class="col-md-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('backend/common.modifier')}}</h3>
                                            </div>
                                            <div class="card-body table-responsive p-0">
                                                <table id="productList"
                                                       class="table m-0">
                                                    <thead>
                                                    <tr>
                                                        <th id="id">{{trans('backend/common.no')}}</th>
                                                        <th>{{trans('backend/common.name')}}</th>
                                                        <th>{{trans('backend/product.price')}}</th>
                                                        <th>{{trans('backend/common.status')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($productData->modifier as $ak1 => $av1)
                                                        <tr>
                                                            <td>{{++$ak1}}</td>
                                                            <td>{{$av1->name}}</td>
                                                            <td>{{$av1->price}}</td>
                                                            <td>
                                                                @if($av1->status == 1)
                                                                    <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                                @else
                                                                    <span class="badge badge-warning">{{trans('backend/common.no')}}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(count($productData->attribute)>0)
                                    <div class="col-md-6">
                                        <div class="card card-primary card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{trans('backend/common.attribute')}}</h3>
                                                </div>
                                                <div class="card-body table-responsive p-0">
                                                    <table id="productList"
                                                           class="table m-0">
                                                        <thead>
                                                        <tr>
                                                            <th id="id">{{trans('backend/common.no')}}</th>
                                                            <th>{{trans('backend/common.name')}}</th>
                                                            <th>{{trans('backend/product.price')}}</th>
                                                            <th>{{trans('backend/common.status')}}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($productData->attribute as $ak1 => $av1)
                                                            <tr>
                                                                <td>{{++$ak1}}</td>
                                                                <td>{{$av1->name}}</td>
                                                                <td>{{$av1->price}}</td>
                                                                <td>
                                                                    @if($av1->status == 1)
                                                                        <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                                    @else
                                                                        <span class="badge badge-warning">{{trans('backend/common.no')}}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    </div>
                                @endif
                                @if(count($productData->branch)>0)
                                    <div class="col-md-6">
                                        <div class="card card-primary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('backend/common.branch')}}</h3>
                                            </div>
                                            <div class="card-body table-responsive p-0">
                                                <table id="productList"
                                                       class="table m-0">
                                                    <thead>
                                                    <tr>
                                                        <th id="id">{{trans('backend/common.no')}}</th>
                                                        <th>{{trans('backend/common.name')}}</th>
                                                        <th>{{trans('backend/inventory.warning_stock_level')}}</th>
                                                        <th>{{trans('backend/common.display_order')}}</th>
                                                        <th>{{trans('backend/common.status')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($productData->branch as $ak1 => $av1)
                                                        <tr>
                                                            <td>{{++$ak1}}</td>
                                                            <td>{{$av1->name}}</td>
                                                            <td>{{$av1->warningStockLevel}}</td>
                                                            <td>{{$av1->display_order}}</td>
                                                            <td>
                                                                @if($av1->status == 1)
                                                                    <span class="badge badge-success">{{trans('backend/common.yes')}}</span>
                                                                @else
                                                                    <span class="badge badge-warning">{{trans('backend/common.no')}}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>                                                       
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection