@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | Rooms
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Rooms
                <div class="card-action">
                    <a href="{{ route("dashboard.room.create") }}"><u><span>Create New Room</span></u></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room ID</th>
                                <th>Room Name</th>
                                <th>Price Per Night</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rooms as $room)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $room->room_id }}</td>
                                    <td>{{ $room->name }}</td>
                                    <td>RM {{ number_format($room->price, 2) }}</td>
                                    <td style="color: {{ $room->statusColor() }};">{{ $room->status() }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.room.edit", ["room" => $room]) }}">
                                            <i class="zmdi zmdi-edit text-white"></i>
                                        </a>
                                        <a class="deleteRoom" data-id="{{ $room->id }}" data-name="{{ $room->room_id }}" style="cursor: pointer">
                                            <i class="zmdi zmdi-delete text-white"></i>
                                        </a>
                                        <a href="{{ route("dashboard.room.view", ["room" => $room]) }}">
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
                "columnDefs": [
                {
                    "targets": 5,
                    "width": "10%",
                    "orderable": false,
                    "searchable": false
                }]
            });
            $(".deleteRoom").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.room.destroy', ':id') }}";
                var roomId = $(this).data("id");
                var roomName = $(this).data("name");
                var url = DELETE_URL.replace(":id", roomId);
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to remove " + roomName + "?",
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
                                    window.location.href = "{{ route("dashboard.room") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
