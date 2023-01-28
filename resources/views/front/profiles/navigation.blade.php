<div class="col-lg-3">
    <div class="sidebar">
        <nav class="navbar"> 
            <div class="navbar-nav w-100 admin">
                <a href="{{ url('profile') }}" class="nav-item nav-link"><i class="fa fa-user"></i>Account</a>
                <a href="{{ url('update-profile') }}" class="nav-item nav-link"><i class="fa fa-user-plus"></i>Account information</a>
                <a href="/addresses" class="nav-item nav-link"><i class="fa fa-map-marker"></i>Delivery address</a>
                <hr>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="d-block" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>{{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </nav>
    </div>
</div>