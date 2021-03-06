@extends("dashboard.layouts.template")

@push("css")
<style>
    .card-body>.row {
        margin-top: 1em;
    }
    select option {
        background-color: transparent;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black;
    }
    .select2.select2-container {
        border: 1px solid #aaa;
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
                <div class="row col-12 mb-5">
                    <div class="card-title">Turnovers (The rooms where someone is departing, and a new customer is arriving on the same day)</div>
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
                                @forelse ($turnovers as $room)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $room->room_id }}</td>
                                        <td>{{ $room->name }}</td>
                                        <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->statusName(true)) !!}</td>
                                        <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                        <td class="text-center action-col">
                                            {{-- @if (Auth::guard("employee")->user()->isAccessible("housekeeper", "admin") && $room->arrival()->count() > 0 && $room->arrival()[0]->status() == 0 && in_array($room->status(), [0, 1]))
                                                <a href="{{ route("dashboard.reservation.check-in", ["reservation" => $room->arrival()[0]]) }}" title="Check In">
                                                    <i class="fa fa-download text-white"></i>
                                                </a>
                                            @endif --}}
                                            <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                <i class="zmdi zmdi-eye text-white"></i>
                                            </a>
                                            @if (!$room->isOccupied() && $room->status() == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() != 4 && Auth::guard("employee")->user()->isAccessible("admin"))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() == 5 && $room->housekeeper == Auth::guard("employee")->user())
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status2-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $room->reservedBy() != null)
                                                <a href="{{ route("dashboard.payment.create", ["reservation" => $room->reservedBy()]) }}" title="Check Out">
                                                    <i class="zmdi zmdi-check text-white"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Turnover Room Today</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row col-12 mb-5">
                    <div class="card-title">Arrivals (The rooms where customers will be arriving on today)</div>
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
                                @forelse ($arrivals as $room)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $room->room_id }}</td>
                                        <td>{{ $room->name }}</td>
                                        <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->statusName(true)) !!}</td>
                                        <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                        <td class="text-center action-col">
                                            {{-- @if (Auth::guard("employee")->user()->isAccessible("housekeeper", "admin") && $room->arrival()->count() > 0 && $room->arrival()[0]->status() == 0 && in_array($room->status(), [0, 1]))
                                                <a href="{{ route("dashboard.reservation.check-in", ["reservation" => $room->arrival()[0]]) }}" title="Check In">
                                                    <i class="fa fa-download text-white"></i>
                                                </a>
                                            @endif --}}
                                            @if (Auth::guard("employee")->user()->isAccessible("housekeeper") && $room->status() == 2 && $room->housekeeper == null)
                                                <a class="self-assign" style="cursor: pointer" data-toggle="modal" data-target="#self-assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Self Assign">
                                                    <i class="ti ti-brush text-white"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                <i class="zmdi zmdi-eye text-white"></i>
                                            </a>
                                            @if (!$room->isOccupied() && $room->status() == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() != 4 && Auth::guard("employee")->user()->isAccessible("admin"))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() == 5 && $room->housekeeper == Auth::guard("employee")->user())
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status2-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $room->reservedBy() != null)
                                                <a href="{{ route("dashboard.payment.create", ["reservation" => $room->reservedBy()]) }}" title="Check Out">
                                                    <i class="zmdi zmdi-check text-white"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Arrival Room Today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row col-12 mb-5">
                    <div class="card-title">Departures (The rooms where customers departing today and do not have any customers arriving in that room today)</div>
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
                                @forelse ($departures as $room)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $room->room_id }}</td>
                                        <td>{{ $room->name }}</td>
                                        <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->statusName(true)) !!}</td>
                                        <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                        <td class="text-center action-col">
                                            {{-- @if (Auth::guard("employee")->user()->isAccessible("housekeeper", "admin") && $room->arrival()->count() > 0 && $room->arrival()[0]->status() == 0 && in_array($room->status(), [0, 1]))
                                                <a href="{{ route("dashboard.reservation.check-in", ["reservation" => $room->arrival()[0]]) }}" title="Check In">
                                                    <i class="fa fa-download text-white"></i>
                                                </a>
                                            @endif --}}
                                            <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                <i class="zmdi zmdi-eye text-white"></i>
                                            </a>
                                            @if (!$room->isOccupied() && $room->status() == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                                <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" title="Assign">
                                                    <i class="fa fa-user-plus text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() != 4 && Auth::guard("employee")->user()->isAccessible("admin"))
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if ($room->status() == 5 && $room->housekeeper == Auth::guard("employee")->user())
                                                <a class="update-status" style="cursor: pointer" data-toggle="modal" data-target="#status2-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}" data-status="{{ $room->status }}" title="Update Status">
                                                    <i class="icon-settings text-white"></i>
                                                </a>
                                            @endif
                                            @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $room->reservedBy() != null)
                                                <a href="{{ route("dashboard.payment.create", ["reservation" => $room->reservedBy()]) }}" title="Check Out">
                                                    <i class="zmdi zmdi-check text-white"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Departure Room Today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Assign Kousekeeper Modal -->
<form action="{{ route("dashboard.room.assign") }}" method="POST">
    @csrf
    <div class="modal fade overflow-hidden" id="assign-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Housekeeper for <span id="assign-room-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row mx-2">
                        <label for="housekeeper">Housekeeper</label>
                        <select class="form-control" id="housekeeper" name="housekeeper">
                            @foreach ($housekeepers as $housekeeper)
                            <option value="{{ $housekeeper->id }}">{{ $housekeeper->username }} ({{ $housekeeper->housekeepRooms->count() }} {{ Str::plural("room", $housekeeper->housekeepRooms->count()) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Self Assign Kousekeeper Modal -->
<form action="{{ route("dashboard.room.self-assign") }}" method="POST">
    @csrf
    <div class="modal fade overflow-hidden" id="self-assign-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Self Assign Housekeeper for <span id="self-assign-room-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p style="color: black">Are you sure you want to assign yourself to <span id="self-assign-room-name"></span>?</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <input type="submit" class="btn btn-primary" value="Yes">
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Update Status Modal -->
<form action="{{ route("dashboard.room.status") }}" method="POST">
    @csrf
    <div class="modal fade overflow-hidden" id="status-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Room Status for <span id="status-room-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row mx-2">
                        <label for="status">Room Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="0">Available</option>
                            @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                <option value="2">Dirty</option>
                            @endif
                            <option value="3">Repairing</option>
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="note">Note for the room (Optional)</label>
                        <textarea name="note" id="note" class="form-control" style="border: 1px solid #aaa; height:150px; color:black !important"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Update Status From Housekeeper Modal -->
<form action="{{ route("dashboard.room.status2") }}" method="POST">
    @csrf
    <div class="modal fade overflow-hidden" id="status2-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Please provided any note for Room <span id="status2-room-id"></span> if necessary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row mx-2">
                        <label for="note">Note for the room (Optional)</label>
                        <textarea name="note" id="note" class="form-control" style="border: 1px solid #aaa; height:150px; color:black !important"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" name="action" value="Repair">
                    <input type="submit" class="btn btn-primary" name="action" value="Done Cleaning">
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push("script")
    <script>
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

            $('#self-assign-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var roomID = button.data('room');
                var modal = $(this);
                modal.find('#self-assign-room-id')[0].innerHTML = roomID;
                modal.find('#self-assign-room-name')[0].innerHTML = roomID;
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
                modal.find('select[name="status"]').val(status).change();
            });

            $('#status2-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var room_id = button.data('room');
                var note = button.data('note');
                var status = button.data('status');
                var modal = $(this);
                modal.find('#status2-room-id')[0].innerHTML = room_id;
                modal.find('input[name="id"]').val(id);
                modal.find('textarea[name="note"]').val(note);
                modal.find('select[name="status"]').val(status).change();
            });

        });
    </script>
@endpush
