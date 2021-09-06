<!DOCTYPE html>
<html lang="en">
    <head>
        @include('dashboard.layouts.head')
        <title>@yield('title')</title>
        @include('dashboard.layouts.css')
    </head>

    <body class="bg-theme bg-theme3">
        <div id="wrapper">
            @include('dashboard.layouts.sidebar')
            @include('dashboard.layouts.header')

            <div class="clearfix"></div>
            <div class="content-wrapper">
                <div class="container-fluid">
                    @yield('content')
                    <div class="overlay toggle-menu"></div>
                </div>
            </div>
            @include('dashboard.layouts.footer')
        </div>
        @include('dashboard.layouts.scripts')
    </body>
</html>
