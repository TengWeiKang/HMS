@extends("dashboard.layouts.template")

@push("css")
<style>
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
                                    <td style="color: {{ $room->statusColor() }};">{!! nl2br($room->status(true)) !!}</td>
                                    <td style="white-space:break-spaces">{!! $room->note !!}</td>
                                    <td class="text-center action-col">
                                        @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                        <a href="{{ route("dashboard.room.edit", ["room" => $room]) }}" title="Edit">
                                            <i class="zmdi zmdi-edit text-white"></i>
                                        </a>
                                        <a class="deleteRoom" data-id="{{ $room->id }}" data-name="{{ $room->room_id }}" style="cursor: pointer" title="Delete">
                                            <i class="zmdi zmdi-delete text-white"></i>
                                        </a>
                                        @endif
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
                        </tbody>
                    </table>
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
                                                    <option value="{{ $housekeeper->id }}">{{ $housekeeper->username }}</option>
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
                                                <option value="2">Dirty</option>
                                                <option value="3">Repairing</option>
                                                <option value="1">Closed</option>
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
                </div>
            </div>
        </div>
    </div>
</div>
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
    </script>
@endpush
