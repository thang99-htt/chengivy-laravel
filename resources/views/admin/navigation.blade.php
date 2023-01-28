<div class="container-xxl position-relative bg-white d-flex p-0">
    <!-- Sidebar Start -->
    <div class="sidebar pe-4 pb-3">
        <nav class="navbar bg-light navbar-light">
            <a href="/" class="navbar-brand mb-3">
                <!-- Navigation Links -->
                <div class="sm:-my-px sm:mt-2 sm:flex">
                    <x-application-logo/>
                </div>
            </a>
            <div class="d-flex align-items-center ms-4 mb-4">
                <div class="position-relative">
                    <img class="rounded-circle" src="/storage/images/admin/photos/{{ Auth::guard('admin')->user()->image }}" alt="" style="width: 40px; height: 40px;">
                    <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                </div>
                <div class="ms-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <h6 class="mb-0">{{ Auth::guard('admin')->user()->fullname }}</h6>
                        </x-slot>
                        <x-slot name="content"></x-slot>
                    </x-dropdown>
                </div>
            </div>
            
            <div class="navbar-nav w-100 admin">
                <a href="/admin/dashboard" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="/admin/banners" class="nav-item nav-link"><i class="fa fa-map"></i>Banners</a>
                @if(Auth::guard('admin')->user()->role_id == 1)
                    <a href="/admin/staffs" class="nav-item nav-link"><i class="fa fa-user"></i>Staffs</a>
                @endif
                @can('isSuperAdmin')
                    <a href="/admin/staffs" class="nav-item nav-link"><i class="fa fa-user"></i>Staffs</a>
                @endcan
                <a href="/admin/categories" class="nav-item nav-link"><i class="fa fa-sitemap me-2"></i>Categories</a>
                <a href="/admin/products" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Products</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle d-block" data-bs-toggle="dropdown"><i class="fa fa-wrench">
                        </i>Settings</a>
                    <div class="dropdown-menu bg-transparent border-0">
                        <a href="{{ url('admin/update-admin-password') }}" class="dropdown-item">Update Password</a>
                        <a href="{{ url('admin/update-admin-details') }}" class="dropdown-item">Update Details</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="" class="nav-link dropdown-toggle d-block" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Pages</a>
                    <div class="dropdown-menu bg-transparent border-0">
                        <a href="/" class="dropdown-item">Home Page</a>
                        <a href="/login" class="dropdown-item">Sign In</a>
                        <a href="/register" class="dropdown-item">Sign Up</a>
                        <a href="/404" class="dropdown-item">404 Error</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- Sidebar End -->


    <!-- Content Start -->
    <div class="content position-relative">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
            <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
            </a>
            <a href="#" class="sidebar-toggler flex-shrink-0">
                <i class="fa fa-bars"></i>
            </a>
            <form class="d-none d-md-flex ms-4">
                <input class="form-control border-0" type="search" placeholder="Search">
            </form>
            <div class="navbar-nav align-items-center ms-auto">
                <div class="nav-item dropdown">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img class="rounded-circle me-lg-2" src="/storage/images/admin/photos/{{ Auth::guard('admin')->user()->image }}" alt="" style="width: 40px; height: 40px;">
                                <span class="d-none d-lg-inline-flex">{{ Auth::guard('admin')->user()->name }}</span>
                            </a>
                        </x-slot>
    
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <x-slot name="content">
                                <form method="POST" action="{{ url('admin/logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="url('admin/logout')"
                                            onclick="event.preventDefault();
                                                       this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </div> 
                    </x-dropdown>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->
        
        
        <!-- Page Content -->
        <main class="mb-100">
            {{ $slot }}
        </main>

        <!-- Footer Start -->
        <div class="container-fluid pt-4 px-4 position-absolute bottom-0">
            <div class="bg-light rounded-top p-4">
                <div class="col-12 col-sm-6 text-center text-sm-start">
                    &copy; <a href="/">Chengivy</a>, All Right Reserved. 
                </div>
            </div>
        </div>
        <!-- Footer End -->
    </div>
    <!-- Content End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>
</div>
