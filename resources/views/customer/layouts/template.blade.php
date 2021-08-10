<!doctype html>
<html lang="en">
    <head>
        @include('customer.layouts.head')
        <title>@yield('title')</title>
        @include('customer.layouts.css')
    </head>
    <body>
        @include('customer.layouts.header')
        @if (Route::currentRouteName() == "customer.home")
            @include('customer.layouts.banner')
        @else
            @include('customer.layouts.breadcrumb')
        @endif

        @yield('content')

        <!--================ Accomodation Area  =================-->
        @include('customer.layouts.scripts')
    </body>
</html>
