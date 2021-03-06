@extends("dashboard.layouts.template")

@section("title")
    Dashboard | {{ $roomType->name }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Image Preview</div>
                <hr>
                <div class="hotel_img text-center">
                    <img id="hotel_preview" class="mw-100" src="{{ $roomType->imageSrc() }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#info" data-toggle="pill" class="nav-link active"><i class="icon-home"></i> <span class="hidden-xs">Room Type Info</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#rooms" data-toggle="pill" class="nav-link"><i class="fa fa-hotel"></i> <span class="hidden-xs">Rooms</span></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="info">
                    <h5 class="mb-3 font-weight-bold">Room Type Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tr>
                                        <td width="20%">Room Type Name:</td>
                                        <td>{{ $roomType->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Single Bed:</td>
                                        <td>{{ $roomType->single_bed }}</td>
                                    </tr>
                                    <tr>
                                        <td>Double Bed:</td>
                                        <td>{{ $roomType->double_bed }}</td>
                                    </tr>
                                    <tr>
                                        <td>Number of Rooms:</td>
                                        <td>{{ $roomType->rooms->count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Facilities:</td>
                                        <td>
                                            @forelse ($roomType->facilities->pluck("name")->toArray() as $facility)
                                                {{ $facility }}<br>
                                            @empty
                                                <span style="color: #F33">No Facilities for this room</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="rooms">
                    <h5 class="mb-3 font-weight-bold">Rooms</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Room ID</th>
                                            <th>Room Name</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roomType->rooms as $room)
                                            <tr>
                                                <td>{{ $room->room_id}}</td>
                                                <td>{{ $room->name }}</td>
                                                <td style="color: {{ $room->statusColor() }}">{!! $room->statusName(true) !!}</td>
                                                <td class="text-center">
                                                    @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                                        <a href="{{ route("dashboard.room.edit", ["room" => $room]) }}" title="Edit">
                                                            <i class="zmdi zmdi-edit text-white" style="font-size: 18px"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" title="View">
                                                        <i class="zmdi zmdi-eye text-white" style="font-size: 18px"></i>
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
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $("#table").DataTable({
                columnDefs: [
                    {
                        targets: 3,
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
