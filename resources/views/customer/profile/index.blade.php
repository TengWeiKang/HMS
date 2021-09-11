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
    Your Profile
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="card-title">User Profile</div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                                <td width="20%">Username:</td>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Phone number:</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td>Created Date:</td>
                                <td>{{ $user->created_at->format("d F Y") }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <a href="{{ route("customer.profile.edit") }}" class="btn btn-primary mt-4">Edit Your Profile</a>
            </div>
        </div>
    </div>
</div><!--End Row-->
@endsection
