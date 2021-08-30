@extends("dashboard.layouts.template")

@push("css")
<style>
    .card-body>.row {
        margin-top: 1em;
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
            <div class="card-header">Attention of Rooms ({{ Carbon\Carbon::today()->format("d F Y") }})</div>
            <div class="card-body">
                <div class="row col-12">
                    <div class="card-title">Turnovers</div>
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Room ID</th>
                                    <th>Room Name</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($turnovers->count() > 0)
                                    @foreach ($turnovers as $room)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $room->room_id }}</td>
                                            <td>{{ $room->name }}</td>
                                            <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->status(true)) !!}</td>
                                            <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                            <td class="text-center action-col">
                                                <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                    <i class="zmdi zmdi-eye text-white"></i>
                                                </a>
                                                @if (!$room->isReserved() && $room->status == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("staff", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                                @endif
                                                @if ($room->status(false) != "Reserved" && ($room->housekeeper == Auth::guard("employee")->user() || Auth::guard("employee")->user()->isAccessible("staff", "admin")))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">No Turnover Room Today</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="card-title">Departures</div>
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Room ID</th>
                                    <th>Room Name</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($departures->count() > 0)
                                    @foreach ($departures as $room)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $room->room_id }}</td>
                                            <td>{{ $room->name }}</td>
                                            <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->status(true)) !!}</td>
                                            <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                            <td class="text-center action-col">
                                                <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                    <i class="zmdi zmdi-eye text-white"></i>
                                                </a>
                                                @if (!$room->isReserved() && $room->status == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("staff", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                                @endif
                                                @if ($room->status(false) != "Reserved" && ($room->housekeeper == Auth::guard("employee")->user() || Auth::guard("employee")->user()->isAccessible("staff", "admin")))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">No Departure Room Today</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="card-title">Arrivals</div>
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Room ID</th>
                                    <th>Room Name</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($arrivals->count() > 0)
                                    @foreach ($arrivals as $room)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $room->room_id }}</td>
                                            <td>{{ $room->name }}</td>
                                            <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->status(true)) !!}</td>
                                            <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                            <td class="text-center action-col">
                                                <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                    <i class="zmdi zmdi-eye text-white"></i>
                                                </a>
                                                @if (!$room->isReserved() && $room->status == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("staff", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                                @endif
                                                @if ($room->status(false) != "Reserved" && ($room->housekeeper == Auth::guard("employee")->user() || Auth::guard("employee")->user()->isAccessible("staff", "admin")))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">No Arrival Room Today</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    {{-- <script>
        $(document).ready(function () {
            $('select#housekeeper, select#status').select2();
            $('.select2.select2-container').addClass('form-control');

            $('#assign-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var roomID = button.data('room');
                var modal = $(this);
                modal.find('#assign-room-id')[0].innerHTML = roomID;
                modal.find('input[name="id"]').val(id);
            });

            $('#status-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var room_id = button.data('room');
                var note = button.data('note');
                var status = button.data('status');
                var modal = $(this);
                modal.find('#status-room-id')[0].innerHTML = room_id;
                modal.find('input[name="id"]').val(id);
                modal.find('textarea[name="note"]').val(note);
                // modal.find('select[name="status"]').val(status).change();
            });

            $("#table").DataTable({
                "columnDefs": [
                {
                    "targets": 6,
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
    </script> --}}
@endpush
