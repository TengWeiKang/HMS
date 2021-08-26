@extends("dashboard.layouts.template")

@push("css")
<style>
    select option {
        background-color: transparent;
    }
</style>
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
                                <th width="5%">#</th>
                                <th width="10%">Room ID</th>
                                <th width="15%">Room Name</th>
                                <th width="10%">Price Per Night</th>
                                <th width="20%">Status</th>
                                <th>Note</th>
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
                                    <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->status()) !!}</td>
                                    <td>{!! nl2br($room->note) !!}</td>
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
                                        @if (!$room->isReserved() && $room->status == 2 && $room->housekept == null)
                                        <a class="assign" data-id="{{ $room->id }}" style="cursor: pointer">
                                            <i class="fa fa-user-plus text-white"></i>
                                        </a>
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
    {{-- {{ dd($housekeepers) }} --}}
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
            $(".assign").on("click", function () {
                var roomId = $(this).data("id");
                Swal.fire({
                    title: "Select a Housekeeper to clean the room",
                    input: "select",
                    inputOptions: {
                        @foreach ($housekeepers as $housekeeper)
                        "{{ $housekeeper->id }}" : "{{ $housekeeper->username }}",
                        @endforeach
                    },
                    inputPlaceholder: "Select Housekeeper",
                    showCancelButton: true
                }).then((result) => {
                    console.log(roomId);
                    if (result.isConfirmed) {
                        if (result.value == "") {
                            Swal.fire("Error!", "Please select a housekeeper", "error");
                        }
                        else {
                            $.ajax({
                                type: "POST",
                                url: "{{ route("dashboard.room.note") }}",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "room": roomId,
                                    "housekeptBy": result.value
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: "Note Updated",
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
                    }
                });
            });
        });
    </script>
@endpush
