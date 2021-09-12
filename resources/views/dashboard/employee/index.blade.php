@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | Employee
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Employees
                <div class="card-action">
                    @php
                        $text = "Employee";
                        if (isset($role)) {
                            switch ($role) {
                                case 0:
                                    $text = "Admin";
                                    break;
                                case 1:
                                    $text = "Frontdesk";
                                    break;
                                case 2:
                                    $text = "Housekeeper";
                                    break;
                            }
                        }
                    @endphp
                    @if ($text == "Employee")
                        <a href="{{ route("dashboard.employee.create") }}"><u><span>Create New {{$text}}</span></u></a>
                    @else
                        <a href="{{ route("dashboard.employee.create", ["role" => $role]) }}"><u><span>Create New {{$text}}</span></u></a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ htmlentities($employee->username) }}</td>
                                    <td>{{ htmlentities($employee->email) }}</td>
                                    <td>{{ htmlentities($employee->phone) }}</td>
                                    @switch($employee->role)
                                        @case(0)
                                            <td>Admin</td>
                                            @break
                                        @case(1)
                                            <td>Frontdesk</td>
                                            @break
                                        @case(2)
                                            <td>Housekeeper</td>
                                            @break
                                        @default
                                    @endswitch
                                    <td>{{ $employee->created_at->format("d M Y") }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.employee.edit", ["employee" => $employee]) }}">
                                            <i class="zmdi zmdi-edit text-white"></i>
                                        </a>
                                        <a class="deleteEmployee" data-id="{{ $employee->id }}" data-username="{{ $employee->username }}" style="cursor: pointer">
                                            <i class="zmdi zmdi-delete text-white"></i>
                                        </a>
                                        <a href="{{ route("dashboard.employee.view", ["employee" => $employee]) }}">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $("#table").DataTable({
                "columnDefs": [{
                    "targets": 6,
                    "orderable": false,
                    "searchable": false
                }]
            });
            $(".deleteEmployee").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.employee.destroy', ':id') }}";
                var empId = $(this).data("id");
                var empUsername = $(this).data("username");
                var url = DELETE_URL.replace(":id", empId);
                Swal.fire({
                    title: "Delete Employee",
                    text: "Are you sure you want to remove " + empUsername + "?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (response){
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response["success"],
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1000,
                                }).then(() => {
                                    window.location.href = "{{ route("dashboard.employee") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
