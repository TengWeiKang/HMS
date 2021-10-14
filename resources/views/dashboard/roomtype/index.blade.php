@extends("dashboard.layouts.template")

@section("title")
    Dashboard | Room Type
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Room Type
                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                    <div class="card-action">
                        <a href="{{ route("dashboard.room-type.create") }}"><u><span>Create New Room Type</span></u></a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room Type Name</th>
                                <th>Price</th>
                                <th>Single bed</th>
                                <th>Double bed</th>
                                <th># of facilities</th>
                                <th># of rooms</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roomTypes as $roomType)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $roomType->name }}</td>
                                    <td>RM {{ number_format($roomType->price, 2) }}</td>
                                    <td>{{ $roomType->single_bed }}</td>
                                    <td>{{ $roomType->double_bed }}</td>
                                    <td>{{ $roomType->facilities->count() }}</td>
                                    <td>{{ $roomType->rooms->count() }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.room-type.view", ["roomType" => $roomType]) }}" title="View">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
                                        @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                            <a href="{{ route("dashboard.room-type.edit", ["roomType" => $roomType]) }}" title="Edit">
                                                <i class="zmdi zmdi-edit text-white"></i>
                                            </a>
                                            @if ($roomType->rooms->count() == 0)
                                            <a class="deleteRoomType" data-id="{{ $roomType->id }}" data-room-type="{{ $roomType->name }}" style="cursor: pointer" title="Delete">
                                                <i class="zmdi zmdi-delete text-white"></i>
                                            </a>
                                            @endif
                                        @endif
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
                "columnDefs": [
                {
                    "targets": 7,
                    "width": "15%",
                    "orderable": false,
                    "searchable": false
                }]
            });
            $(".deleteRoomType").on("click", function () {
                var roomTypeID = $(this).data("id");
                var roomTypeName = $(this).data("room-type");
                const DELETE_URL = "{{ route('dashboard.room-type.destroy', ':id') }}";
                var url = DELETE_URL.replace(":id", roomTypeID);
                Swal.fire({
                    title: "Delete Room Type",
                    text: "Are you sure you want to remove " + roomTypeName + "?",
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
                                    window.location.href = "{{ route("dashboard.room-type") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
