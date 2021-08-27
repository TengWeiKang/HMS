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
                    <table id="table" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room ID</th>
                                <th>Room Name</th>
                                <th>Price (1 night)</th>
                                <th>Status</th>
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
                                    <td style="white-space:break-spaces">{!! nl2br($room->note) !!}</td>
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
                                        @if ($room->housekept == Auth::guard("employee")->user() || true)
                                        <a class="clear" data-id="{{ $room->id }}" style="cursor: pointer">
                                            <i class="icon-notebook text-white"></i>
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
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $("#table").DataTable({
                "responsive": true,
                "columnDefs": [
                {
                    "targets": 5,
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
            $(".clear").on("click", function () {
                var roomId = $(this).data("id");
                Swal.fire({
                    title: "Update the room status",
                    html: '<input type="radio" value="1" name="status" class="swal2-radio" style="margin: 1em 1em 0"/>' +
                        '<span class="swal2-label mr-5">Available</span>' +
                        '<input type="radio" value="-1" name="status" class="swal2-radio ml-5" style="margin: 1em 1em 0"/>' +
                        '<span class="swal2-label mr-5">Repair</span>' +
                        '<div style="text-align: left"><label style="text-transform: initial; color: black; margin-left:3em; margin-top: 3em; margin-bottom: 0;" for="note">Note (Optional)</label>' +
                        '<textarea class="swal2-textarea" style="width: -webkit-fill-available; resize: none; height: 150px; font-size: 16px" name="note"></textarea></div>',
                    showCancelButton: true
                });

            });
        });
    </script>
@endpush
