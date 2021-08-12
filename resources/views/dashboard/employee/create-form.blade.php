@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | New Employee
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Employee Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.employee.create") }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control form-control-rounded @error("username") border-danger @enderror" name="username" placeholder="Username" value="{{ old("username") }}">
                        @error("username")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-rounded @error("email") border-danger @enderror" name="email" placeholder="Email Address" value="{{ old("email") }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number (E.g. 012-3456789)</label>
                        <input type="text" class="form-control form-control-rounded @error("phone") border-danger @enderror" name="phone" placeholder="Mobile Number" value="{{ old("phone") }}">
                        @error("phone")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control form-control-rounded" name="role" @if (isset($role) && $role >= 0 && $role <= 2) disabled @endif>
                            <option value="1" @if (isset($role) && $role == 0) selected @endif>Admin</option>
                            <option value="2" @if (isset($role) && $role == 1) selected @endif>Staff</option>
                            <option value="3" @if (isset($role) && $role == 2) selected @endif>Housekeeper</option>
                        </select>
                    </div>
                    <span class="text-warning">Password will be randomly generated and send to the email</span>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script></script>
@endpush
