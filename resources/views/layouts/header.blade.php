@php
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\Cart;
    $categories = Category::categories();
    
    if(Auth::check()) {
        $carts = Cart::with(['product', 'size'])->where('user_id', Auth::user()->id)->get()->toArray();
        $cartCount = Cart::with(['product', 'size'])->where('user_id', Auth::user()->id)->count();
    }
@endphp

<header class="header navbar-area">
    <div class="topbar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-4">                        
                    <div class="top-left">
                        <div class="nav-inner">
                            <div class="mega-category-menu">
                                <span class="cat-button">
                                    <i class="lni lni-menu"></i>
                                    <a href="">Shop</a>
                                </span>
                                <div class="sub-category">
                                    <div class="row">
                                        <div class="col-4">
                                            <ul class="">
                                                @foreach ($categories as $category)
                                                <li>
                                                    <a href="/{{ $category['url'] }}">{{ $category['name'] }}
                                                        @if(count($category['childs'])>0)
                                                            <i class="lni lni-chevron-right"></i>
                                                        @endif
                                                    </a>  
                                                    <div class="inner-sub-category">
                                                        <div class="row">
                                                            <div class="col-3">
                                                                <ul class="">
                                                                    <li>
                                                                        @foreach ($category['childs'] as $child)
                                                                            @if($child['status'] == 1)
                                                                            <a href="/{{ $child['url'] }}">
                                                                                {{ $child['name'] }}
                                                                            </a>
                                                                            @endif
                                                                        @endforeach
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-9">
                                                                <img src="{{ asset('storage/images/logo/topbar-2.jpg') }}" alt="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-8">
                                            <div class="pe-3">
                                                <img src="{{ asset('storage/images/logo/topbar-1.jpg') }}" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="top-middle">
                        <a class="navbar-brand" href="/">
                            {{-- <img src="{{ asset('/storage/images/logo/logo.png') }}" alt="Logo" /> --}}
                            <x-application-logo/>
                        </a>
                    </div>
                </div>
                <div class="col-4">      
                    @if (Route::has('login'))
                        <div class="top-end">
                            @can('isAdmin')
                                <p class="dashboard">
                                    <a href="{{ url('/admin/dashboard') }}">Dashboard</a>
                                    <i class="lni lni-chevron-right"></i>
                                </p>
                            @else
                                <div class="navbar-search">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="navbar-cart">
                                    <div class="cart-items">
                                        <a href="
                                            @auth {{ url('carts') }}
                                            @else {{ route('login') }}
                                            @endauth " class="main-btn">
                                            <svg x.ms-automns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>
                                            </svg>
                                            @auth
                                            <span class="total-items">{{ $cartCount }}</span>
                                            @endauth
                                        </a>
        
                                        @auth
                                            @if($cartCount>0)
                                            <div class="shopping-item">
                                                <div class="dropdown-cart-header d-flex justify-content-between">
                                                    <h6>Cart</h6>
                                                    <span>{{ $cartCount }} Items</span>
                                                </div>
                                                <ul class="shopping-list">
                                                    @php $totalOrders = 0; @endphp
                                                    @foreach($carts as $item)
                                                    @php 
                                                        $getDiscountPrice = Product::getDiscountPrice($item['id']);
                                                        $totalPrice = 0;
                                                    @endphp
                                                    <li>  
                                                        <a href="javascript:void(0)" class="confirmDeleteFront remove" title="Remove this item"
                                                        module="item" moduleid="{{ $item['id'] }}" modulename="{{ $item['id'] }}">
                                                            <i class="lni lni-close"></i>
                                                        </a>
                                                        <div class="cart-img-head">
                                                            <a class="cart-img" href="product-details.html"><img src="{{ asset('storage/images/products/'.$item['product']['image']) }}" alt="#" /></a>
                                                        </div>
                                                        <div class="content">
                                                            <h4><a href="product-details.html">{{ $item['product']['name'] }}</a></h4>
                                                            <p>Size: {{ $item['size']['name'] }}</p>
                                                            <p class="quantity">
                                                                {{ $item['quantity'] }}x - 
                                                                <span class="amount">
                                                                    @if($getDiscountPrice > 0)
                                                                    {{ $getDiscountPrice }}
                                                                    @else {{ $item['product']['price'] }}$
                                                                </span>
                                                            </p>
                                                            @endif
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <div class="bottom mt-4">
                                                    <div class="total">
                                                        <span>Total</span>
                                                        <span class="total-amount">$134.00</span>
                                                    </div>
                                                    <div class="button">
                                                        <a href="{{ url('carts') }}" class="btn animate">View Cart</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <div class="shopping-item">
                                                    <h6>CART</h6>
                                                    <img src="{{ asset('storage/images/cart/empty-cart.svg') }}" alt="" />
                                                    <p class="m-3 text-center text-dark">You have no items in your shopping cart.</p>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>

                                @auth
                                    <div class="navbar-user">
                                        <ul class="navbar-nav user-login">
                                            <a href="javascript:void(0)">
                                                <i class="lni lni-user"></i>
                                                <span class="total-items">Hi</span>
                                            </a>
                                            <li class="nav-item">
                                                <ul class="sub-menu">
                                                    <li class="nav-item">
                                                        <a href="{{ url('/profile') }}" class="">Profiles</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <!-- Authentication -->
                                                        <form method="POST" action="{{ route('logout') }}">
                                                            @csrf
                                                            <a href="{{ route('logout') }}"
                                                                    onclick="event.preventDefault();
                                                                            this.closest('form').submit();">
                                                                {{ __('Log Out') }}
                                                            </a>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </li>
                                            
                                        </ul>
                                    </div> 
                                @else  
                                    <div class="navbar-user">
                                        <ul class="navbar-nav user-login">
                                            <a href="javascript:void(0)">
                                                <i class="lni lni-user"></i>
                                            </a>
                                            <li class="nav-item">
                                                <ul class="sub-menu">
                                                    <li class="nav-item">
                                                        <a href="{{ route('login') }}" class="">Log in</a>
                                                    </li>
                                                    @if (Route::has('register'))
                                                        <li class="nav-item">
                                                            <a href="{{ route('register') }}" class="">Register</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>  
                                @endauth

                            @endcan
                        </div>
                    @endif 
                </div>
            </div>
        </div>
    </div>

</header>

  <!-- Modal -->
<div class="modal fade modal-search" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div class="modal-title flex-grow-1" id="exampleModalLabel">
              {{-- <form method="GET" action=""> --}}
                <div class="d-flex align-items-center">
                        <input type="search" name="search" id="search" class="form-control me-3 w-75" placeholder="Enter search name">
                        <button type="submit" data-bs-toggle="modal" data-bs-target="#exampleModal" class="border-0 bg-transparent">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                    </div>
                {{-- </form> --}}
          </div>
          <button type="button" class="btn text-dark" data-bs-dismiss="modal">Close</button>
        </div>
        <div class="modal-body">
            <div id="search_list" class="product-list">

            </div>
        </div>
      </div>
    </div>
</div>

