@isset($roomGroups)
    @forelse ($roomGroups as $roomTypeName => $singleBeds)
        @foreach ($singleBeds as $singleBed => $doubleBeds)
            @foreach ($doubleBeds as $doubleBed => $rooms)
                <div class="col-lg-3 col-sm-6">
                    <div class="accomodation_item text-center">
                        <div class="hotel_img">
                            <img src="{{ $rooms[0]->type->imageSrc() }}" alt="Hotel Placeholder">
                            @auth("customer")
                                <a href="{{ route("customer.booking.create", ["roomType" => $rooms[0]->type, "singleBed" => $singleBed, "doubleBed" => $doubleBed, "startDate" => $startDate, "endDate" => $endDate]) }}" class="btn theme_btn button_hover">Book Now</a>
                            @endauth
                        </div>
                        <h3 class="sec_h4"><a href="{{ route("customer.room.view", ["roomType" => $rooms[0]->type, "singleBed" => $singleBed, "doubleBed" => $doubleBed, "startDate" => $startDate, "endDate" => $endDate]) }}">{{ $roomTypeName }}</a></h3>
                        <h4 style="font-size: 16px; color: black">{{ $singleBed }} single bed</h4>
                        <h4 style="font-size: 16px; color: black">{{ $doubleBed }} double bed</h4>
                        <h6>{{ $rooms->count() }} available</h6>
                        <h5>RM {{ number_format($rooms[0]->type->price, 2) }}<small>/night</small></h5>
                    </div>
                </div>
            @endforeach
        @endforeach
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
