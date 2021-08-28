@extends("dashboard.layouts.template")

@push("css")
<style>
    select option {
        background-color: transparent;
    }
    .modal .h1, .modal .h2, .modal .h3, .modal .h4, .modal .h5, .modal .h6, .modal h1, .modal h2, .modal h3, .modal h4, .modal h5, .modal h6, .modal label {
        color: black;
        text-transform: initial;
        font-size: inherit;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black;
    }
    .select2.select2-container {
        border: 1px solid #444;
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
                                    <td style="white-space:break-spaces">{!! $room->note !!}</td>
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
                                        <a class="assign" style="cursor: pointer" data-toggle="modal" data-target="#assign-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}">
                                            <i class="fa fa-user-plus text-white"></i>
                                        </a>
                                        @endif
                                        @if ($room->status == 2 && ($room->housekept == Auth::guard("employee")->user() || Auth::guard("employee")->user()->isAdmin() || Auth::guard("employee")->user()->isStaff()))
                                        <a class="clear" style="cursor: pointer" data-toggle="modal" data-target="#clear-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}">
                                            <i class="icon-flag text-white"></i>
                                        </a>
                                        @endif
                                        @if ($room->status == 3 && (Auth::guard("employee")->user()->isAdmin() || Auth::guard("employee")->user()->isStaff()))
                                        <a class="clear" style="cursor: pointer" data-toggle="modal" data-target="#repair-modal" data-id="{{ $room->id }}" data-room="{{ $room->room_id }}" data-note="{{ $room->note }}">
                                            <i class="icon-wrench text-white"></i>
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
                        <div class="modal fade" id="assign-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Housekeeper for <span id="assign_room_id"></span></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row mx-4">
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
                    <!-- Clean Room Modal -->
                    <form action="{{ route("dashboard.room.clean") }}" method="POST">
                        @csrf
                        <div class="modal fade" id="clear-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Room Status for <span id="room_id"></span></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group text-center">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="available" name="status" class="custom-control-input" style="width: 25%" value="0" required>
                                                <label class="custom-control-label" for="available">Available</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="repair" name="status" class="custom-control-input" style="width: 15%" value="3">
                                                <label class="custom-control-label" for="repair">Repair</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
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
                    <!-- Repair Room Modal -->
                    <form action="{{ route("dashboard.room.repair") }}" method="POST">
                        @csrf
                        <div class="modal fade" id="repair-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Room Status for <span id="repair_room_id"></span></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group text-center">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="repair-available" name="status" class="custom-control-input" style="width: 25%" value="0" required>
                                                <label class="custom-control-label" for="repair-available">Available</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="repair-closed" name="status" class="custom-control-input" style="width: 15%" value="1">
                                                <label class="custom-control-label" for="repair-closed">Closed</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="note">Note for the room (Optional)</label>
                                            <textarea name="note" id="repair-note" class="form-control" style="border: 1px solid #aaa; height:150px; color:black !important"></textarea>
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
            $('select#housekeeper').select2();
            $('.select2.select2-container').addClass('form-control form-control-rounded');
            // $('.select2-selection--multiple').parents('.select2-container').addClass('form-select-multiple');

            $('#assign-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var roomID = button.data('room');
                var modal = $(this);
                modal.find('#assign_room_id')[0].innerHTML = roomID;
                modal.find('input[name="id"]').val(id);
                console.log(id, roomID, modal.find('#assign_room_id')[0], modal.find('input[name="id"]'));
            });

            $('#clear-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var room_id = button.data('room');
                var modal = $(this);
                modal.find('#room_id')[0].innerHTML = room_id;
                modal.find('input[name="id"]').val(id);
            });

            $('#repair-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var id = button.data('id');
                var room_id = button.data('room');
                var note = button.data('note');
                var modal = $(this);
                console.log(note);
                console.log(modal);
                modal.find('#repair_room_id')[0].innerHTML = room_id;
                modal.find('input[name="id"]').val(id);
                modal.find('textarea[name="note"]').val(note);
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
            // $(".assign").on("click", function () {
            //     var roomId = $(this).data("id");
            //     Swal.fire({
            //         title: "Select a Housekeeper to clean the room",
            //         input: "select",
            //         inputOptions: {
            //             @foreach ($housekeepers as $housekeeper)
            //             "{{ $housekeeper->id }}" : "{{ $housekeeper->username }}",
            //             @endforeach
            //         },
            //         inputPlaceholder: "Select Housekeeper",
            //         showCancelButton: true
            //     }).then((result) => {
            //         console.log(roomId);
            //         if (result.isConfirmed) {
            //             if (result.value == "") {
            //                 Swal.fire("Error!", "Please select a housekeeper", "error");
            //             }
            //             else {
            //                 $.ajax({
            //                     type: "POST",
            //                     url: "{{ route("dashboard.room.assign") }}",
            //                     data: {
            //                         "_token": "{{ csrf_token() }}",
            //                         "room": roomId,
            //                         "housekeptBy": result.value
            //                     },
            //                     success: function (response) {
            //                         Swal.fire({
            //                             title: "Note Updated",
            //                             text: response["success"],
            //                             icon: 'success',
            //                             showConfirmButton: false,
            //                             timer: 1000,
            //                         }).then(() => {
            //                             window.location.href = "{{ route("dashboard.room") }}";
            //                         });
            //                     }
            //                 });
            //             }
            //         }
            //     });
            // });
        });
    </script>
@endpush
