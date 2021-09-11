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
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="profile">
                    <h5 class="mb-3 font-weight-bold">User Profile</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="20%">Username:</td>
                                            <td>{{ $employee->username }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td>{{ $employee->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Phone number:</td>
                                            <td>{{ $employee->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>Role:</td>
                                            <td>{{ $employee->role() }}</td>
                                        </tr>
                                        <tr>
                                            <td>Created Date:</td>
                                            <td>{{ $employee->created_at->format("d F Y") }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
