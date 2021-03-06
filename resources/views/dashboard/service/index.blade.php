@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | Room Services
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Room Services
                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                    <div class="card-action">
                        <a href="{{ route("dashboard.service.create") }}"><u><span>Create New Service</span></u></a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Service Name</th>
                                <th>Service Price</th>
                                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td>RM {{ number_format($service->price, 2) }}</td>
                                    @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                        <td class="text-center action-col">
                                            <a href="{{ route("dashboard.service.edit", ["service" => $service]) }}" title="Edit">
                                                <i class="zmdi zmdi-edit text-white"></i>
                                            </a>
                                            <a class="deleteRoomService" data-id="{{ $service->id }}" data-name="{{ $service->name }}" style="cursor: pointer" title="Delete">
                                                <i class="zmdi zmdi-delete text-white"></i>
                                            </a>
                                        </td>
                                    @endif
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
                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                "columnDefs": [
                {
                    "targets": 3,
                    "width": "10%",
                    "orderable": false,
                    "searchable": false
                }]
                @endif
            });
            $(".deleteRoomService").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.service.destroy', ':id') }}";
                var serviceID = $(this).data("id");
                var serviceName = $(this).data("name");
                var url = DELETE_URL.replace(":id", serviceID);
                Swal.fire({
                    title: "Delete Room Service",
                    text: "Are you sure you want to remove " + serviceName + "?",
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
                                    window.location.href = "{{ route("dashboard.service") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
