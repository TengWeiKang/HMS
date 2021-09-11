@extends("dashboard.layouts.template")

@php
    $user = Auth::guard('employee')->user();
@endphp

@push("css")

@endpush

@section("title")
    Dashboard | {{ $user->username }}
@endsection

@section("content")
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                <li class="nav-item">
                    <a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Profile</span></a>
                </li>
            </ul>
        </div>
        <div class="tab-content p-3">
            <div class="tab-pane active" id="profile">
                <h5 class="mb-3 font-weight-bold">User Profile</h5>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mt-3">Username: {{ $user->username }}</h6>
                        <h6 class="mt-3">Email: {{ $user->email }}</h6>
                        <h6 class="mt-3">Phone number: {{ $user->phone }}</h6>
                        <h6 class="mt-3">Role: {{ $user->role() }}</h6>
                        <h6 class="mt-3">Created Date: {{ $user->created_at->format("d M Y") }}</h6>
                    </div>
                </div>
                <a href="{{ route("dashboard.profile.edit") }}" class="btn btn-primary mt-4">Edit Your Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection
