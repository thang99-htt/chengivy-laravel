@php use App\Models\Product; @endphp

<div class="container-fluid">
    <div class="row">       
        @foreach($products as $product)
        @php 
            $getDiscountPrice = Product::getDiscountPrice($product['id']);
        @endphp
        <div class="col-lg-3 col-md-3 col-12 mb-5">
            <div class="single-product">
                <div class="product-image">
                    <img src="{{ asset('storage/images/products/'.$product['image']) }}" alt="#" />
                    @if($getDiscountPrice > 0)
                    <span class="sale-tag">SALE</span>
                    @endif
                    <div class="button">
                        <a href="/product/{{ $product['id']}}" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                    </div>
                </div>
                <div class="product-info">
                    <span class="category">{{ $product['category']['name'] }}</span>
                    <h4 class="title">
                        <a href="/product/{{ $product['id'] }}">{{ $product['name'] }}</a>
                    </h4>
                    <div class="price">
                        @if($getDiscountPrice > 0)
                        <span>{{ $getDiscountPrice }}$</span>
                        <span class="text-decoration-line-through float-end text-secondary">{{ $product['price'] }}$</span> <br>
                        @else 
                            <span>{{ $product['price'] }}$</span> <br>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

    
