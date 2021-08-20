@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Reset Password
@endsection

@section("title2")
    Password Reset
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Reset Password Form</div>
				@if (session('status'))
					<div class="text-danger row mb-3 col-sm-6 offset-sm-3">{{ session('status') }}</div>
				@endif
                <hr>
                <form action="{{ route("password.reset", ['token' => $token]) }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <input type="hidden" name="token" id="token" value="{{ $token }}">
                        <label for="email">Email</label>
                        <input type="text" class="form-control form-control-rounded @error("email") border-danger @enderror" name="email" placeholder="Enter Your Email" value="{{ old("email") }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control form-control-rounded @error("password") border-danger @enderror" name="password" placeholder="New Password">
                        @error("password")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="password_confirmation">Confirm Your Password</label>
                        <input type="password" class="form-control form-control-rounded @error("password_confirmation") border-danger @enderror" name="password_confirmation" placeholder="Confirm Password">
                        @error("password_confirmation")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-light btn-round px-5 w-100"> Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!--End Row--
@endsection
