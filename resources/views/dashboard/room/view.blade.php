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
                            <h6 class="mt-3">Room ID: {{ $room->room_id }}</h6>
                            <h6 class="mt-3">Room Name: {{ $room->name }}</h6>
                            <h6 class="mt-3">Price: RM {{ number_format($room->price, 2) }}</h6>
                            <h6 class="mt-3">Single Bed: {{ $room->single_bed }}</h6>
                            <h6 class="mt-3">Double Bed: {{ $room->double_bed }}</h6>
                            <h6 class="mt-3">Status: <span style="color: {{ $room->statusColor() }}">{{ $room->status() }}</span></h6>
                            <h6 class="mt-3">Created Date: {{ $room->created_at->format("d M Y") }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script></script>
@endpush
