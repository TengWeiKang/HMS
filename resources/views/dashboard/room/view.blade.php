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
    Dashboard | {{ $room->room_id . " " . $room->name }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Image Preview</div>
                <hr>
                <div class="hotel_img text-center">
                    <img id="hotel_preview" class="mw-100" src="{{ $room->type->imageSrc() }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
        @if (!$room->isOccupied() && $room->status() == 2 && $room->housekeeper == null && Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
            <button type="button" class="btn btn-secondary w-100 mb-3" data-toggle="modal" data-target="#assign-modal">Assign Housekeeper</button>
        @endif
        @if ($room->status() != 4 && Auth::guard("employee")->user()->isAccessible("admin"))
            <button type="button" class="btn btn-primary w-100 mb-3" data-toggle="modal" data-target="#status-modal">Update Status</button>
        @endif
        @if ($room->status() == 5 && $room->housekeeper == Auth::guard("employee")->user())
            <button type="button" class="btn btn-primary w-100 mb-3" data-toggle="modal" data-target="#status2-modal">Update Status</button>
        @endif
        @if (Auth::guard("employee")->user()->isAccessible("housekeeper") && $room->status() == 2 && $room->housekeeper == null)
            <button type="button" class="btn btn-primary w-100 mb-3" data-toggle="modal" data-target="#self-assign-modal">Self Assign Housekeeper</button>
        @endif
        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $room->reservedBy() != null)
            <a class="btn btn-primary w-100 mb-3" href="{{ route("dashboard.payment.create", ["reservation" => $room->reservedBy()]) }}" title="Check Out">
                Customer Check Out
            </a>
        @endif
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
                @endif
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#room" data-toggle="pill" class="nav-link active"><i class="icon-home"></i> <span class="hidden-xs">Room Info</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#history" data-toggle="pill" class="nav-link"><i class="fa fa-history"></i> <span class="hidden-xs">History</span></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="room">
                    <h5 class="mb-3 font-weight-bold">Room Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tr>
                                        <td width="20%">Room ID:</td>
                                        <td>{{ $room->room_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Room Name:</td>
                                        <td>{{ $room->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Room Type:</td>
                                        <td>
                                            @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                                <a class="hyperlink" href="{{ route("dashboard.room-type.view", ["roomType" => $room->type]) }}">{{ $room->type->name }}</a>
                                            @else
                                                {{ $room->type->name }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Single Bed:</td>
                                        <td>{{ $room->single_bed }}</td>
                                    </tr>
                                    <tr>
                                        <td>Double Bed:</td>
                                        <td>{{ $room->double_bed }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td style="color: {{ $room->statusColor() }}">{!! nl2br($room->statusName(true)) !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Created Date:</td>
                                        <td>{{ $room->created_at->format("d F Y") }}</td>
                                    </tr>
                                    <tr>
                                        <td>Facilities:</td>
                                        <td>
                                            @forelse ($room->type->facilities as $facility)
                                                {{ $facility->name }}<br>
                                            @empty
                                                <span style="color: #F33">No Facilities for this room</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Note:</td>
                                        <td>
                                            @if (is_null($room->note))
                                                <span><i>No Note is provided</i></span>
                                            @else
                                                <span style="white-space:break-spaces">{!! $room->note !!}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="history">
                    <h5 class="mb-3 font-weight-bold">History</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Customer Full Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($room->reservations as $history)
                                            <tr>
                                                <td><a class="hyperlink" href="{{ route("dashboard.customer.view", $history->customer) }}">{{ $history->customer->fullName()}}</a></td>
                                                <td>{{ $history->start_date->format("d F Y") }}</td>
                                                <td>{{ $history->end_date->format("d F Y") }}</td>
                                                <td style="color: {{ $history->statusColor() }}">{{ $history->statusName() }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route("dashboard.reservation.view", ["reservation" => $history]) }}">
                                                        <i class="zmdi zmdi-eye text-white" style="font-size: 18px"></i>
                                                    </a>
                                                    @if ($history->payment != null)
                                                    <a href="{{ route("dashboard.payment.view", ["payment" => $history->payment]) }}">
                                                        <i class="fa fa-dollar text-white" style="font-size: 18px"></i>
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
        </div>
    </div>
    <!-- Assign Kousekeeper Modal -->
    <form action="{{ route("dashboard.room.assign") }}" method="POST">
        @csrf
        <div class="modal fade overflow-hidden" id="assign-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Housekeeper for {{ $room->room_id }}</h5>
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
                        <input type="hidden" name="id" value="{{ $room->id }}">
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
                        <h5 class="modal-title">Update Room Status for {{ $room->room_id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row mx-2">
                            <label for="status">Room Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="0" @if ($room->status() == 0) selected @endif>Available</option>
                                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                    <option value="2" @if ($room->status() == 2) selected @endif>Dirty</option>
                                @endif
                                <option value="3" @if ($room->status() == 3) selected @endif>Repairing</option>
                            </select>
                        </div>
                        <div class="form-group row mx-2">
                            <label for="note">Note for the room (Optional)</label>
                            <textarea name="note" id="note" class="form-control" style="border: 1px solid #aaa; height:150px; color:black !important">{{ $room->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="{{ $room->id }}">
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
                        <h5 class="modal-title">Please provided any note for Room {{ $room->room_id }} if necessary</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row mx-2">
                            <label for="note">Note for the room (Optional)</label>
                            <textarea name="note" id="note" class="form-control" style="border: 1px solid #aaa; height:150px; color:black !important">{{ $room->note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="{{ $room->id }}">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" name="action" value="Repair">
                        <input type="submit" class="btn btn-primary" name="action" value="Done Cleaning">
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
                        <h5 class="modal-title">Self Assign Housekeeper for {{ $room->room_id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p style="color: black">Are you sure you want to assign yourself to {{ $room->room_id }}?</p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" value="{{ $room->id }}">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <input type="submit" class="btn btn-primary" value="Yes">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('select#housekeeper, select#status').select2();
            $("#table").DataTable({
                columnDefs: [
                {
                    "targets": 4,
                    "orderable": false,
                    "searchable": false
                }]
            });
            $('.select2.select2-container').addClass('form-control');
        });
    </script>
@endpush
