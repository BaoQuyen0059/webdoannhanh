<footer class="footer_dark">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="widget">
                        <div class="footer_logo">
                            <a href="{{ route('client.home') }}"><img
                                    src="{{ hwa_setting('site_logo_light') ? hwa_image_url("system", hwa_setting('site_logo_light')) : "shopwise/assets/images/logo_light.png" }}"
                                    alt="logo" height="40"/></a>
                        </div>
                        <p>{{ hwa_setting('site_description', "If you are going to use of Lorem Ipsum need to be sure there isn't hidden of text") }}</p>
                    </div>
                    <div class="widget">
                        <ul class="social_icons social_white">
                            <li><a href="{{ hwa_setting('site_social_facebook', 'javascript:void(0);') }}" @if(hwa_setting('site_social_facebook')) target="_blank" @endif><i class="ion-social-facebook"></i></a></li>
                            <li><a href="{{ hwa_setting('site_social_twitter', 'javascript:void(0);') }}" @if(hwa_setting('site_social_twitter')) target="_blank" @endif><i class="ion-social-twitter"></i></a></li>
                            <li><a href="{{ hwa_setting('site_social_youtube', 'javascript:void(0);') }}" @if(hwa_setting('site_social_youtube')) target="_blank" @endif><i class="ion-social-youtube-outline"></i></a></li>
                            <li><a href="{{ hwa_setting('site_social_instagram', 'javascript:void(0);') }}" @if(hwa_setting('site_social_instagram')) target="_blank" @endif><i class="ion-social-instagram-outline"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">Liên kết</h6>
                        <ul class="widget_links">
                            <li><a href="{{ route('client.about') }}">Về chúng tôi</a></li>
                            <li><a href="#">Hỏi đáp</a></li>
                            <li><a href="{{ route('client.term') }}">Chính sách và điều khoản</a></li>
                            <li><a href="{{ route('client.delivery') }}">Chính sách giao hàng</a></li>
                            <li><a href="{{ route('client.returns') }}">Chính sách đổi trả</a></li>
                            <li><a href="{{ route('client.contact') }}">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">Danh mục</h6>
                        <ul class="widget_links">
                            @foreach($global_categories as $global_category)
                                <li><a href="{{ route('client.category.show', ['slug' => $global_category['slug']]) }}">{{ $global_category['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">Tài khoản</h6>
                        <ul class="widget_links">
                            <li><a href="{{ route('client.customers.index') }}">Tài khoản của tôi</a></li>
                            <li><a href="{{ route('client.cart.index') }}">Giỏ hàng</a></li>
                            <li><a href="{{ route('client.wishlist.index') }}">Danh sách yêu thích</a></li>
                            <li><a href="{{ route('client.customers.orders.index') }}">Lịch sử đặt hàng</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget">
                        <h6 class="widget_title">Thông tin liên hệ</h6>
                        <ul class="contact_info contact_info_light">
                            <li>
                                <i class="ti-location-pin"></i>
                                <p>{{ hwa_setting('site_address', '123 Street, Old Trafford, NewYork, USA') }}</p>
                            </li>
                            <li>
                                <i class="ti-email"></i>
                                <a href="mailto:{{ hwa_setting('site_email', hwa_app_contact()) }}">{{ hwa_setting('site_email', hwa_app_contact()) }}</a>
                            </li>
                            <li>
                                <i class="ti-mobile"></i>
                                <p>{{ hwa_setting('site_phone', '123-456-7890') }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom_footer border-top-tran">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-md-0 text-center text-md-left">© {{ date('Y') }} All Rights Reserved by <a
                            href="{{ route('client.home') }}">{{ hwa_app_name() }}</a></p>
                </div>
                <div class="col-md-6">
                    <ul class="footer_payment text-center text-lg-right">
                        <li><a href="https://vnpay.vn" target="_blank"><img src="shopwise/assets/images/vnpay.png" alt="VN Pay" height="32"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
