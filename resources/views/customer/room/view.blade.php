@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | {{ $room->name }}
@endsection

@section("title2")
    {{ $room->name }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Image Preview</div>
                <hr>
                <div class="accomodation_item mb-0">
                    <div class="hotel_img text-center border border-secondary">
                        <img class="mw-100" src="data:{{ $room->image_type }};base64,{{ base64_encode($room->room_image) }}" alt="Hotel PlaceHolder">
                    </div>
                </div>
            </div>
        </div>
        @auth("customer")
            <a href="#" class="btn btn-primary mt-4 w-100">Book Now</a>
        @endauth
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="card-title">User Profile</div>
                <hr>
                <div class="row">
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
                                <td>{{ $room->type->name }}</td>
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
                                <td>Facilities:</td>
                                <td>
                                    @forelse ($room->facilities->pluck("name")->toArray() as $facility)
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
    </div>
</div>
@endsection
