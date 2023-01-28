@php use App\Models\Product; @endphp

@extends('layouts.default')

@section('content')
    <section class="section mt-50">
        <div class="detail">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-8">
                        <div class="detail-image">
                            <div class="container">
                                <div class="row">
                                    <div class="col-2">
                                        <div class="d-flex flex-column">
                                            <div class="left-image">
                                                <img class="demo" src="{{ asset('/storage/images/products/'.$productDetails['image']) }}" onclick="currentSlide(1)" alt="">
                                            </div>
                                            
                                            <?php $start = 2; ?>
                                            @foreach($productDetails['images'] as $image)
                                                <div class="left-image">
                                                    <img class="demo" src="{{ asset('/storage/images/products/'.$image['image']) }}" onclick="currentSlide(<?= $start++; ?>)" alt="">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="mySlides">
                                            <img src="{{ asset('/storage/images/products/'.$productDetails['image']) }}">
                                        </div>
                                        @foreach($productDetails['images'] as $image)
                                            <div class="mySlides">
                                                <img src="{{ asset('/storage/images/products/'.$image['image']) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST" action="{{ url('cart/add') }}">
                            @csrf
                            <div class="detail-info">
                                <input type="hidden" name="product_id" id="" value="{{ $productDetails['id'] }}">
                                <div class="info-name">
                                    <h3>{{ $productDetails['name'] }}</h3>
                                </div>
                                <div class="info-category">
                                    <p>{{ $productDetails['category']['name'] }}</p>
                                </div>
                                <div class="info-price">
                                    @php $getDiscountPrice = Product::getDiscountPrice($productDetails['id']); @endphp
                                    @if($getDiscountPrice > 0)
                                    <p class="text-decoration-line-through text-secondary">{{ $productDetails['price'] }}$</p>
                                    <p>{{ $getDiscountPrice }}$</p>
                                    @else 
                                    <p>{{ $productDetails['price'] }}$</p>
                                    @endif
                                </div>
                                <hr>
                                <div class="info-des">
                                    <p>{{ $productDetails['description'] }}</p>
                                </div>
                                <div class="info-size">
                                    <div class="d-flex justify-content-between">
                                        <p>Size</p>
                                        <p class="guide">Size guide</p>
                                    </div>
                                    
                                    <select name="product_size" class="form-select" aria-label="Default select example">
                                        <option selected disabled>-- Select Size --</option>
                                        @foreach($productDetails['sizes'] as $size)
                                        <option value="{{ $size['id'] }}">{{ $size['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="info-quantity">
                                    <div class="d-flex align-items-end">
                                        <p>Quantity</p>
                                        <div class="number-input">
                                            <a onclick="this.parentNode.querySelector('input[type=number]').stepDown()" >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                                </svg>
                                            </a>
                                            <input class="quantity" min="1" name="product_quantity" value="1" type="number">
                                            <a onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="info-add">
                                    <div class="row">
                                        <div class="d-grid gap-2 col-6">
                                            <button class="btn wish" type="button">Add to Wishlist</button>
                                        </div>
                                        <div class="d-grid gap-2 col-6">
                                            <button class="btn bag" type="submit">Add to Bag</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("demo");
            let captionText = document.getElementById("caption");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            captionText.innerHTML = dots[slideIndex-1].alt;
        }
    </script>
@endsection
