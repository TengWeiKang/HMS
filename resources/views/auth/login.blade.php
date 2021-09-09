@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | Login
@endsection

@section("title2")
    Login Your Account
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Login Form</div>
                @if (session('status'))
					<div class="text-danger text-center">{{ session('status') }}</div>
				@endif
				@if (session('message'))
					<div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("login") }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="username">Username</label>
                        <input type="text" class="form-control form-control-rounded @error("username") border-danger @enderror" name="username" placeholder="Enter Your Username" value="{{ old("username") }}">
                        @error("username")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control form-control-rounded @error("password") border-danger @enderror" name="password" placeholder="Enter Password">
                        @error("password")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group mt-3 custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" checked>
                        <label class="custom-control-label" for="remember">Remember Me</label>
                    </div>

                    {{-- <div class="form-group py-2">
                        <input type="checkbox" name="remember" checked=""/>
                        <label for="remember">Remember me</label>
                    </div> --}}

                    <div class="form-group mt-3">
                        <a href="{{ route("password.forget") }}">Forgotten Password? Click Here</a>
                    </div>

                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-round px-5 w-100"><i class="icon-lock"></i> Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!--End Row-->
@endsection
