@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | Register
@endsection

@section("title2")
    Register an Account
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Register Form</div>
                <hr>
                <form action="{{ route("register") }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("username") border-danger @enderror" name="username" placeholder="Enter Your Username" value="{{ old("username") }}">
                        @error("username")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="passport">NRIC / Passport <span class="text-danger">*</span></label>
                        <input type="passport" class="form-control form-control-rounded @error("passport") border-danger @enderror" name="passport" placeholder="Enter Your NRIC / Passport" value="{{ old("passport") }}">
                        @error("passport")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-rounded @error("email") border-danger @enderror" name="email" placeholder="Enter Your Email Address" value="{{ old("email") }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label for="firstName">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("firstName") border-danger @enderror" id="firstName" name="firstName" placeholder="First Name" value="{{ old("firstName") }}">
                            @error("firstName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="lastName">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("lastName") border-danger @enderror" id="lastName" name="lastName" placeholder="Last Name" value="{{ old("lastName") }}">
                            @error("lastName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("phone") border-danger @enderror" name="phone" placeholder="Enter Your Contact Number (E.g. 012-3456789)" value="{{ old("phone") }}">
                        @error("phone")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-rounded @error("password") border-danger @enderror" name="password" placeholder="Enter Password" autocomplete="off">
                        @error("password")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-rounded @error("password_confirmation") border-danger @enderror" name="password_confirmation" placeholder="Confirm Password" autocomplete="off">
                        @error("password_confirmation")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- <div class="form-group py-2">
                        <div class="icheck-material-white">
                            <input type="checkbox" name="remember" checked=""/>
                            <label for="user-checkbox2">Remember me</label>
                        </div>
                    </div> --}}
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-round px-5 w-100"><i class="icon-lock"></i> Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
