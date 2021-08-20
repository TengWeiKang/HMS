@extends("dashboard.layouts.template")

@push("css")

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
                    <img id="hotel_preview" class="mw-100" src="data:{{ $room->image_type }};base64,{{ base64_encode($room->room_image) }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                <li class="nav-item">
                    <a href="javascript:void();" data-target="#room" data-toggle="pill" class="nav-link active"><i class="icon-home"></i> <span class="hidden-xs">Room Info</span></a>
                </li>
            </ul>
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
                                        <td>Price:</td>
                                        <td>RM {{ number_format($room->price, 2) }}</td>
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
                                        <td style="color: {{ $room->statusColor() }}">{{ $room->status() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Created Date:</td>
                                        <td>{{ $room->created_at->format("d F Y") }}</td>
                                    </tr>
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
