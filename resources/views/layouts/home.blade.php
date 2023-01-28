@php
    use App\Models\Category;
    use App\Models\Product;
    $categories = Category::categories();
    $imageCategoryFirst = Category::where('parent_id', 1)->first()->toArray();
@endphp
<section class="hero-area">
    <div class="container-fluid">
        <div class="row">
            <video width="100%" height="auto" autoplay loop muted>
                <source src="{{ asset('/storage/images/hero/CL.mp4') }}" type="video/mp4">
            </video>
        </div>
        <div class="row pt-3">
            <div class="col-lg-8 col-12 p-0">
                <div class="slider-head">
                    <div class="hero-slider">
                        @foreach($sliderBanners as $banner)
                        <div class="single-slider" style="background-image: url({{ asset('storage/images/banners/'.$banner['image']) }});">
                            <div class="content">
                                <h2>{{ $banner['name'] }}</h2>
                                <p>{{ $banner['description'] }}</p>
                                <div class="button">
                                    <a  @if(!empty($banner['link']))
                                        href="{{ url('/'.$banner['link']) }}"
                                        @else 
                                        href="javascrip:;"
                                        @endif
                                        class="btn">Discover
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="row">
                    @foreach($fixBanners as $banner)
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="hero-small-banner" style="background-image: url({{ asset('storage/images/banners/'.$banner['image']) }});">
                            <div class="content">
                                <h2>{{ $banner['name'] }}</h2>
                                <p>{{ $banner['description'] }}</p>
                                <div class="button">
                                    <a class="btn" 
                                        @if(!empty($banner['link']))
                                        href="{{ url('admin/update-banner/'.$banner['link']) }}"
                                        @else 
                                        href="javascrip:;"
                                        @endif
                                    >Discover</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($advertiseBanners as $banner)
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="hero-small-banner style2">
                            <div class="content">
                                <h2>{{ $banner['name'] }}</h2>
                                <p>{{ $banner['description'] }}</p>
                                <div class="button">
                                    <a class="btn" 
                                        @if(!empty($banner['link']))
                                        href="{{ url('admin/update-banner/'.$banner['link']) }}"
                                        @else 
                                        href="javascrip:;"
                                        @endif
                                    >Discover</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
</section>

<section class="trending-product section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Trending Product</h2>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
        <div id="carouselExampleControls" class="carousel slide slider-trending" data-bs-ride="carousel" data-bs-interval="500">
            <div class="carousel-inner">
                @foreach($trendingProducts as $product)
                <div class="single-slider carousel-item @if($product['id']==$trendingFirstProduct['id']) active @endif" 
                    style="background-image: url({{ asset('storage/images/products/'.$product['image']) }});">
                    <div class="content">
                        <div class="product-info">
                            <span class="category">{{ $product['category']['name']  }}</span>
                            <h4 class="title">
                                <a href="/product/{{ $product['id'] }}">{{ $product['name'] }}</a>
                            </h4>
                            <ul class="review">
                                <li><i class="lni lni-star-filled"></i></li>
                                <li><i class="lni lni-star-filled"></i></li>
                                <li><i class="lni lni-star-filled"></i></li>
                                <li><i class="lni lni-star-filled"></i></li>
                                <li><i class="lni lni-star-filled"></i></li>
                                <li><span>5.0 Review(s)</span></li>
                            </ul>
                            <div class="price">
                                @php 
                                    $getDiscountPrice = Product::getDiscountPrice($product['id']);
                                @endphp
                                @if($getDiscountPrice>0)
                                <span class="text-decoration-line-through text-secondary">{{ $product['price'] }}$</span> <br>
                                <span>{{ $getDiscountPrice }}$</span>
                                @else 
                                    <span>{{ $product['price'] }}$</span> <br>
                                @endif
                            </div>
                        </div>
                        <div class="button">
                            <a href="/product/{{ $product['id'] }}" class="btn">Discover</a>
                        </div>
                        <div class="image-tail">
                            @foreach($product['images'] as $image)
                            <div class="image-item">
                                <img src="{{ asset('/storage/images/products/'.$image['image']) }}" class="d-block" alt="...">
                            </div>
                            @endforeach
                        </div>
                    </div>                    
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="special-offer section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Special Offer</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="row">
                    @foreach($specialProducts as $product)
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="single-product">
                            <div class="product-image">
                                <img src="{{ asset('storage/images/products/'.$product['image']) }}" alt="#" />
                                <div class="button">
                                    <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="category">{{ $product['category']['name']  }}</span>
                                <h4 class="title">
                                    <a href="/product/{{ $product['id'] }}">{{ $product['name']  }}</a>
                                </h4>
                                <ul class="review">
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><i class="lni lni-star-filled"></i></li>
                                    <li><span>5.0 Review(s)</span></li>
                                </ul>
                                <div class="price">
                                    <span>{{ $product['price']  }}$</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @foreach($specialNewProduct as $product)
                <div class="single-banner right" style="background-image: url('{{ asset('storage/images/products/'.$product['image']) }}'); margin-top: 30px;">
                    <div class="content">
                        <h2><a href="/product/{{ $product['id'] }}">{{ $product['name'] }}</a></h2>
                        <p>{{ $product['description'] }}</p>
                        <div class="price">
                            <span>{{ $product['price'] }}$</span>
                        </div>
                        <div class="button">
                            <a href="/product/{{ $product['id'] }}" class="btn">Shop Now</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @foreach($specialHighestPriceProduct as $product)
            <div class="col-lg-4 col-md-12 col-12">
                <div class="offer-content">
                    <div class="image">
                        <img src="{{ asset('storage/images/products/'.$product['image']) }}" alt="#" />
                        <span class="sale-tag">LIMIT</span>
                    </div>
                    <div class="text">
                        <h2><a href="/product/{{ $product['id'] }}">{{ $product['name'] }}</a></h2>
                        <ul class="review">
                            <li><i class="lni lni-star-filled"></i></li>
                            <li><i class="lni lni-star-filled"></i></li>
                            <li><i class="lni lni-star-filled"></i></li>
                            <li><i class="lni lni-star-filled"></i></li>
                            <li><i class="lni lni-star-filled"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>{{ $product['price'] }}$</span>
                        </div>
                        <p>{{ $product['description'] }}</p>
                    </div>
                    <div class="box-head">
                        <div class="box">
                            <h1 id="days">000</h1>
                            <h2 id="daystxt">Days</h2>
                        </div>
                        <div class="box">
                            <h1 id="hours">00</h1>
                            <h2 id="hourstxt">Hours</h2>
                        </div>
                        <div class="box">
                            <h1 id="minutes">00</h1>
                            <h2 id="minutestxt">Minutes</h2>
                        </div>
                        <div class="box">
                            <h1 id="seconds">00</h1>
                            <h2 id="secondstxt">Secondes</h2>
                        </div>
                    </div>
                    <div style="background: rgb(204, 24, 24)" class="alert">
                        <h1 style="padding: 50px 80px; color: white">
                            We are sorry, Event ended !
                        </h1>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="featured-categories section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Ready To Wear</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="single-category">
                <ul class="">
                    @foreach ($categories as $category)
                        @foreach ($category['childs'] as $child)
                            @if($child['status'] == 1)
                                <li @if($child['id']==$imageCategoryFirst['id']) class="active" @endif>
                                    <a class="heading" href="{{ url($child['url']) }}">{{ $child['name'] }}</a>
                                    <div class="images" style="background-image: url(/storage/images/categories/{{ $child['image'] }});"></div>
                                </li>
                            @endif
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>