@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | Forget Password
@endsection

@section("title2")
    Forget Password
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Forget Password Form</div>
				@if (session('message'))
					<div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("password.forget") }}" method="POST">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="email">Your Email</label>
                        <input type="text" class="form-control form-control-rounded @error("email") border-danger @enderror" name="email" placeholder="Enter Your Email" value="{{ old("email") }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-round px-5 w-100"><i class="icon-lock"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
