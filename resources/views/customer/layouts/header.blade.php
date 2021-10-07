<header class="header_area">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <!-- Brand and toggle get grouped for better mobile display -->
            <a class="navbar-brand logo_h" href="{{ route("customer.home") }}"><img src="{{ asset("customer/image/Logo.png") }}" alt=""><span class="ml-3 font-weight-bold">Hotel Booking</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                <ul class="nav navbar-nav menu_nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route("customer.home") }}">Home</a></li>
                    @auth("customer")
                    <li class="nav-item"><a class="nav-link" href="{{ route("customer.analysis") }}">Statistics</a></li>
                    <li class="nav-item submenu dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bookings</a>
                        <ul class="dropdown-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route("customer.booking") }}">View Ongoing Booking</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route("customer.booking.history") }}">View Booking History</a></li>
                        </ul>
                    </li>
                    @endauth
                    @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route("login") }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route("register") }}">Register</a></li>
                    @endguest
                    @auth("customer")
                        <li class="nav-item submenu dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::guard()->user()->username }}</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route("customer.profile.view") }}">Profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route("customer.profile.password") }}">Change Password</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route("logout") }}">Logout</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>
    </div>
</header>
