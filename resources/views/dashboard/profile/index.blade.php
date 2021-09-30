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
                                <td>Contact Number:</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td>Role:</td>
                                <td>{{ $user->role() }}</td>
                            </tr>
                            <tr>
                                <td>Created Date:</td>
                                <td>{{ $user->created_at->format("d F Y") }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <a href="{{ route("dashboard.profile.edit") }}" class="btn btn-primary mt-4">Edit Your Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection
