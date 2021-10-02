@isset($rooms)
    @forelse ($rooms as $room)
        <div class="col-lg-3 col-sm-6">
            <div class="accomodation_item text-center">
                <div class="hotel_img">
                    <img src="{{ $room->type->imageSrc() }}" alt="Hotel Placeholder">
                    @auth("customer")
                        <a href="{{ route("customer.booking.create", ["room" => $room, "startDate" => $startDate, "endDate" => $endDate]) }}" class="btn theme_btn button_hover">Book Now</a>
                    @endauth
                </div>
                <a href="{{ route("customer.room.view", ["room" => $room]) }}"><h3 class="sec_h4">{{ $room->name }}</h3></a>
                <h4>{{ $room->type->name }}</h4>
                <h5>RM {{ number_format($room->type->price, 2) }}<small>/night</small></h5>
            </div>
        </div>
    @empty
        <div class="text-center w-100">
            <h2>No room is found</h2>
        </div>
        @endforelse
@else
    <div class="text-center w-100">
        <h2>Please filled up arrival and departure date</h2>
    </div>
@endisset
