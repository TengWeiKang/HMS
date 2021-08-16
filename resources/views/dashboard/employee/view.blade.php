@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | {{ $employee->username }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                <li class="nav-item">
                    <a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Profile</span></a>
                </li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="profile">
                    <h5 class="mb-3 font-weight-bold">User Profile</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mt-3">Username: {{ $employee->username }}</h6>
                            <h6 class="mt-3">Email: {{ $employee->email }}</h6>
                            <h6 class="mt-3">Phone number: {{ $employee->phone }}</h6>
                            <h6 class="mt-3">Role: {{ $employee->role() }}</h6>
                            <h6 class="mt-3">Created Date: {{ $employee->created_at->format("d M Y") }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script></script>
@endpush
