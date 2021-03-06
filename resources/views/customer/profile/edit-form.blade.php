@extends("customer.layouts.template")

@push("css")

@endpush

@php
    $user = Auth::user();
@endphp

@section("title")
    Hotel Booking | {{ $user->username }}
@endsection

@section("title2")
    Edit Your Profile
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">User Profile</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("customer.profile.edit") }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("username") border-danger @enderror" name="username" placeholder="Enter Your Username" value="{{ old("username", $user->username) }}">
                        @error("username")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="passport">NRIC / Passport <span class="text-danger">*</span></label>
                        <input type="passport" class="form-control form-control-rounded" name="passport" placeholder="Enter Your NRIC / Passport" value="{{ $user->passport }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-rounded @error("email") border-danger @enderror" name="email" placeholder="Enter Your Email Address" value="{{ old("email", $user->email) }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-round px-5 w-100"> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
