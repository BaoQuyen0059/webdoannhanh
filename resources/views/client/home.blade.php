@extends('client.layouts.index')

@section('client_head')
    <meta name="description" content="{{ hwa_page_title() }}">
    <meta name="keywords" content="{{ hwa_page_title() }}">

    <!-- SITE TITLE -->
    <title>{{ hwa_page_title() }}</title>
@endsection

@section('client_main')

    @if(isset($banners) && count($banners) > 0)
        <!-- START SECTION BANNER -->
        <div class="banner_section slide_medium shop_banner_slider staggered-animation-wrap">
            <div id="carouselExampleControls" class="carousel slide carousel-fade light_arrow" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($banners as $banner)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }} background_bg"
                             data-img-src="{{ hwa_image_url("banners", $banner['image']) }}">
                            <div class="banner_slide_content">
                                <div class="container">
                                    <!-- STRART CONTAINER -->
                                    <div class="row">
                                        <div class="col-lg-7 col-9">
                                            <div class="banner_content overflow-hidden">
                                                <h5 class="mb-3 staggered-animation font-weight-light"
                                                    data-animation="slideInLeft"
                                                    data-animation-delay="0.5s">{{ $banner['sub_title'] ?? "" }}</h5>
                                                <h2 class="staggered-animation" data-animation="slideInLeft"
                                                    data-animation-delay="1s">{{ $banner['title'] ?? "" }}</h2>
                                                <a class="btn btn-fill-out rounded-0 staggered-animation text-uppercase"
                                                   href="{{ $banner['url'] ?? "javascript:void(0);" }}"
                                                   data-animation="slideInLeft"
                                                   data-animation-delay="1.5s" target="{{ $banner['target'] }}">Mua ngay</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END CONTAINER-->
                            </div>
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                   data-slide="prev"><i
                        class="ion-chevron-left"></i></a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                   data-slide="next"><i
                        class="ion-chevron-right"></i></a>
            </div>
        </div>
        <!-- END SECTION BANNER -->
    @endif

    <!-- END MAIN CONTENT -->
    <div class="main_content">

        <!-- START SECTION SHOP -->
        <div class="section pt-10 pb-20">
            <div class="custom-container">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="heading_tab_header">
                                    <div class="heading_s2">
                                        <h4>Món ăn mới</h4>
                                    </div>
                                    <div class="tab-style2">
                                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                                data-target="#tabmenubar" aria-expanded="false">
                                            <span class="ion-android-menu"></span>
                                        </button>
                                        <ul class="nav nav-tabs justify-content-center justify-content-md-end"
                                            id="tabmenubar" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="arrival-tab" data-toggle="tab"
                                                   href="#arrival" role="tab" aria-controls="arrival"
                                                   aria-selected="true">Món Mới</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="sellers-tab" data-toggle="tab" href="#sellers"
                                                   role="tab" aria-controls="sellers" aria-selected="false">Bán Chạy
                                                    Nhất</a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="tab_slider">
                                    <div class="tab-pane fade show active" id="arrival" role="tabpanel"
                                         aria-labelledby="arrival-tab">
                                        @if(isset($product_new) && count($product_new) > 0)
                                            <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1"
                                             data-loop="false" data-margin="20"
                                             data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                                            @foreach($product_new as $newProduct)
                                                <div class="item">
                                                    <div class="product_wrap">
                                                        <span class="pr_flash bg-warning">Mới</span>
                                                        <div class="product_img">
                                                            <a href="{{ route('client.product.show', ['slug' => $newProduct['slug']]) }}">
                                                                <img
                                                                    src="{{ hwa_image_url("products/thumbs", $newProduct['thumb']) }}"
                                                                    alt="{{ $newProduct['name'] ?? "" }}">
                                                                <img class="product_hover_img"
                                                                     src="{{ hwa_image_url("products/thumbs", $newProduct['thumb']) }}"
                                                                     alt="{{ $newProduct['name'] ?? "" }}">
                                                            </a>
                                                            <div class="product_action_box">
                                                                <ul class="list_none pr_action_btn">
                                                                    <li class="add-to-cart"><a
                                                                            href="{{ route('client.cart.create', $newProduct['id']) }}"><i
                                                                                class="icon-basket-loaded"></i> Thêm vào
                                                                            giỏ hàng</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{ route('client.wishlist.store', ['id' => $newProduct['id']]) }}"><i
                                                                                class="icon-heart"></i></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="product_info">
                                                            <h6 class="product_title"><a
                                                                    href="{{ route('client.product.show', ['slug' => $newProduct['slug']]) }}">{{ $newProduct['name'] ?? "" }}</a>
                                                            </h6>
                                                            <div class="product_price">
                                                                <span class="price">{{ number_format($newProduct['price']) ?? 0 }} đ</span>
                                                            </div>
                                                            <div class="rating_wrap">
                                                                <div class="rating">
                                                                    <div class="product_rate"
                                                                         style="width:{{ hwa_rating_percent($newProduct['id']) }}%"></div>
                                                                </div>
                                                                <span class="rating_num">({{ count($newProduct->reviews) ?? 0 }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @else
                                            <p class="text-center">Không có sản phẩm</p>
                                        @endif
                                        @if(isset($product_new) && count($product_new) > 0)
                                            <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1"
                                             data-loop="false" data-margin="20"
                                             data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                                            @foreach($product_new as $newProduct)
                                                <div class="item">
                                                    <div class="product_wrap">
                                                        <span class="pr_flash bg-warning">Mới</span>
                                                        <div class="product_img">
                                                            <a href="{{ route('client.product.show', ['slug' => $newProduct['slug']]) }}">
                                                                <img
                                                                    src="{{ hwa_image_url("products/thumbs", $newProduct['thumb']) }}"
                                                                    alt="{{ $newProduct['name'] ?? "" }}">
                                                                <img class="product_hover_img"
                                                                     src="{{ hwa_image_url("products/thumbs", $newProduct['thumb']) }}"
                                                                     alt="{{ $newProduct['name'] ?? "" }}">
                                                            </a>
                                                            <div class="product_action_box">
                                                                <ul class="list_none pr_action_btn">
                                                                    <li class="add-to-cart"><a
                                                                            href="{{ route('client.cart.create', $newProduct['id']) }}"><i
                                                                                class="icon-basket-loaded"></i> Thêm vào
                                                                            giỏ hàng</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{ route('client.wishlist.store', ['id' => $newProduct['id']]) }}"><i
                                                                                class="icon-heart"></i></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="product_info">
                                                            <h6 class="product_title"><a
                                                                    href="{{ route('client.product.show', ['slug' => $newProduct['slug']]) }}">{{ $newProduct['name'] ?? "" }}</a>
                                                            </h6>
                                                            <div class="product_price">
                                                                <span class="price">{{ number_format($newProduct['price']) ?? 0 }} đ</span>
                                                            </div>
                                                            <div class="rating_wrap">
                                                                <div class="rating">
                                                                    <div class="product_rate"
                                                                         style="width:{{ hwa_rating_percent($newProduct['id']) }}%"></div>
                                                                </div>
                                                                <span class="rating_num">({{ count($newProduct->reviews) ?? 0 }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @else
                                            <p class="text-center">Không có sản phẩm</p>
                                        @endif
                                    </div>
                                    <div class="tab-pane fade" id="sellers" role="tabpanel"
                                         aria-labelledby="sellers-tab">
                                        @if(isset($product_sale) && count($product_sale) > 0)
                                            <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1"
                                             data-loop="false" data-margin="20"
                                             data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                                            @foreach($product_sale as $sale)
                                                <div class="item">
                                                    <div class="product_wrap">
                                                        <span class="pr_flash bg-danger">Hot</span>
                                                        <div class="product_img">
                                                            <a href="{{ route('client.product.show', ['slug' => $sale['slug']]) }}">
                                                                <img
                                                                    src="{{ hwa_image_url("products/thumbs", $sale['thumb']) }}"
                                                                    alt="{{ $sale['name'] ?? "" }}">
                                                                <img class="product_hover_img"
                                                                     src="{{ hwa_image_url("products/thumbs", $sale['thumb']) }}"
                                                                     alt="{{ $sale['name'] ?? "" }}">
                                                            </a>
                                                            <div class="product_action_box">
                                                                <ul class="list_none pr_action_btn">
                                                                    <li class="add-to-cart"><a
                                                                            href="{{ route('client.cart.create', $sale['id']) }}"><i
                                                                                class="icon-basket-loaded"></i> Thêm vào
                                                                            giỏ hàng</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{ route('client.wishlist.store', ['id' => $sale['id']]) }}"><i
                                                                                class="icon-heart"></i></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="product_info">
                                                            <h6 class="product_title"><a
                                                                    href="{{ route('client.product.show', ['slug' => $sale['slug']]) }}">{{ $sale['name'] ?? "" }}</a>
                                                            </h6>
                                                            <div class="product_price">
                                                                <span class="price">{{ $sale['price'] ?? 0 }} đ</span>
                                                            </div>
                                                            <div class="rating_wrap">
                                                                <div class="rating">
                                                                    <div class="product_rate"
                                                                         style="width:{{ hwa_rating_percent($sale['id']) }}%"></div>
                                                                </div>
                                                                <span class="rating_num">({{ count($sale->reviews) ?? 0 }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @else
                                            <p class="text-center">Không có sản phẩm</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECTION SHOP -->

        <!-- START SECTION SHOP -->
        <!-- <div class="section pt-0">
            <div class="custom-container">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="heading_tab_header">
                                    <div class="heading_s2">
                                        <h4>Thương hiệu</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- END SECTION SHOP -->

        <!-- START SECTION SHOP -->
        <div class="section pt-0">
            <div class="custom-container">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="heading_tab_header">
                                    <div class="heading_s2">
                                        <h4>Món ăn hot</h4>
                                    </div>
                                    <div class="view_all">
                                        <a href="{{ route('client.product.index') }}" class="text_default"><i class="linearicons-power"></i> <span>Xem thêm</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @if(isset($product_trending) && count($product_trending) > 0)
                                    <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1"
                                     data-loop="false" data-margin="20"
                                     data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                                    @foreach($product_trending as $trending)
                                        <div class="item">
                                            <div class="product_wrap">
                                                <span class="pr_flash bg-danger">Hot</span>
                                                <div class="product_img">
                                                    <a href="{{ route('client.product.show', ['slug' => $trending['slug']]) }}">
                                                        <img
                                                            src="{{ hwa_image_url("products/thumbs", $trending['thumb']) }}"
                                                            alt="el_img2">
                                                        <img class="product_hover_img"
                                                             src="{{ hwa_image_url("products/thumbs", $trending['thumb']) }}"
                                                             alt="el_hover_img2">
                                                    </a>
                                                    <div class="product_action_box">
                                                        <ul class="list_none pr_action_btn">
                                                            <li class="add-to-cart"><a
                                                                    href="{{ route('client.cart.create', $trending['id']) }}"><i
                                                                        class="icon-basket-loaded"></i> Thêm vào giỏ
                                                                    hàng</a></li>
                                                            <li>
                                                                <a href="{{ route('client.wishlist.store', ['id' => $trending['id']]) }}"><i
                                                                        class="icon-heart"></i></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="product_info">
                                                    <h6 class="product_title"><a
                                                            href="{{ route('client.product.show', ['slug' => $trending['slug']]) }}">{{ $trending['name'] ?? "" }}</a>
                                                    </h6>
                                                    <div class="product_price">
                                                        <span class="price">{{ number_format($trending['price']) ?? 0 }} đ</span>
                                                    </div>
                                                    <div class="rating_wrap">
                                                        <div class="rating">
                                                            <div class="product_rate"
                                                                 style="width:{{ hwa_rating_percent($trending['id']) }}%"></div>
                                                        </div>
                                                        <span
                                                            class="rating_num">({{ count($trending->reviews) ?? 0 }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @else
                                    <p class="text-center">Không có sản phẩm.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECTION SHOP -->

        <!-- START SECTION BLOG -->
        <div class="section pt-0">
            <div class="custom-container">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="heading_tab_header">
                                    <div class="heading_s2">
                                        <h4>Bài viết mới nhất</h4>
                                    </div>
                                    <div class="view_all">
                                        <a href="{{ route('client.news.index') }}" class="text_default"><span>Xem thêm</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    @if(!empty($news) && count($news) > 0)
                                        @foreach($news as $itemNews)
                                            <div class="col-lg-4 col-md-6">
                                                <div class="blog_post blog_style2 box_shadow1">
                                                    <div class="blog_img">
                                                        <a href="{{ route('client.news.show', ['slug' => $itemNews['slug']]) }}">
                                                            <img src="{{ $itemNews['image'] ? hwa_image_url("news/thumb", $itemNews['image']) : "assets/images/blog_small_img1.jpg" }}" alt="{{ $itemNews['title'] ?? "" }}">
                                                        </a>
                                                    </div>
                                                    <div class="blog_content bg-white">
                                                        <div class="blog_text">
                                                            <h5 class="blog_title"><a href="{{ route('client.news.show', ['slug' => $itemNews['slug']]) }}">{{ $itemNews['title'] ?? "" }}</a></h5>
                                                            <ul class="list_none blog_meta">
                                                                <li><i class="ti-calendar"></i> {{ Carbon\Carbon::parse($itemNews['created_at'])->locale('vi')->isoFormat('MMM DD, Y') }}</li>
                                                                <li><i class="ti-eye"></i> {{ number_format($itemNews['views']) ?? 0 }} Lượt xem</li>
                                                            </ul>
                                                            <p>{{ $itemNews['description'] ?? "" }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-center">Không có bài viết mới</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECTION BLOG -->

        <!-- START SECTION SHOP INFO -->
        <div class="section pt-0">
            <div class="container">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="icon_box icon_box_style1">
                            <div class="icon">
                                <i class="flaticon-shipped"></i>
                            </div>
                            <div class="icon_box_content">
                                <h5>Giao hàng miễn phí</h5>
                                <p>Giao hàng miễn phí cho tất cả các đơn đặt hàng tại Việt Nam.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="icon_box icon_box_style1">
                            <div class="icon">
                                <i class="flaticon-money-back"></i>
                            </div>
                            <div class="icon_box_content">
                                <h5>Giá cả tích kiệm</h5>
                                <p>Giá cả món ăn rất sinh viên</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="icon_box icon_box_style1">
                            <div class="icon">
                                <i class="flaticon-support"></i>
                            </div>
                            <div class="icon_box_content">
                                <h5>Hỗ Trợ Trực Tuyến 27/4</h5>
                                <p>Liên hệ với chúng tôi 24 giờ một ngày, 7 ngày một tuần.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECTION SHOP INFO -->

        <!-- START SECTION SUBSCRIBE NEWSLETTER -->
        <div class="section bg_default small_pt small_pb">
            <div class="custom-container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="newsletter_text text_white">
                            <h3>Theo dõi bản tin ngay bây giờ</h3>
                            <p>Đăng ký ngay để cập nhật các chương trình khuyến mãi.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="newsletter_form2 rounded_input">
                            <form action="{{ route('client.newsletter') }}" method="post">
                                @csrf
                                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                       name="email" placeholder="Nhập email của bạn" value="{{ old('email') }}">
                                <button type="submit" class="btn btn-dark btn-radius">Theo dõi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- START SECTION SUBSCRIBE NEWSLETTER -->
    </div>
    <!-- END MAIN CONTENT -->
@endsection
