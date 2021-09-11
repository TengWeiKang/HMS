@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | {{ Auth::user()->username }}
@endsection

@section("title2")
    Change Your Password
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
                <form action="{{ route("customer.profile.password") }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label for="password">Current Password</label>
                        <input type="password" class="form-control form-control-rounded @error("password") border-danger @enderror" name="password" placeholder="Current Password">
                        @error("password")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control form-control-rounded @error("newPassword") border-danger @enderror" name="newPassword" placeholder="New Password">
                        @error("newPassword")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="newPassword_confirmation">Confirm Password</label>
                        <input type="password" class="form-control form-control-rounded @error("newPassword_confirmation") border-danger @enderror" name="newPassword_confirmation" placeholder="Confirm Password">
                        @error("newPassword_confirmation")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-round px-5 w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!--End Row-->
@endsection
