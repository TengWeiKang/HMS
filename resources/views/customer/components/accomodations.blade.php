@forelse ($rooms as $room)
    <div class="col-lg-3 col-sm-6">
        <div class="accomodation_item text-center">
            <div class="hotel_img">
                <img src="data:{{ $room->image_type }};base64,{{ base64_encode($room->room_image) }}" alt="Hotel Placeholder">
                @auth("customer")
                    <a href="#" class="btn theme_btn button_hover">Book Now</a>
                @endauth
            </div>
            <a href="#"><h4 class="sec_h4">{{ $room->name }}</h4></a>
            <h5>RM {{ number_format($room->price, 2) }}<small>/night</small></h5>
        </div>
    </div>
@empty
    <div class="text-center w-100">
        <h2>No room is found</h2>
    </div>
@endforelse
