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
        <div class="container">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        @include('customer.layouts.scripts')
    </body>
</html>
