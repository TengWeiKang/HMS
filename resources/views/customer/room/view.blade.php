@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | {{ $roomType->name }}
@endsection

@section("title2")
    {{ $roomType->name }}
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
                        <img class="mw-100" src="{{ $roomType->imageSrc() }}" alt="Hotel PlaceHolder">
                    </div>
                </div>
            </div>
        </div>
        @auth("customer")
            @isset($startDate, $endDate)
                <a href="{{ route("customer.booking.create", ["roomType" => $roomType, "singleBed" => $singleBed, "doubleBed" => $doubleBed, "startDate" => $startDate, "endDate" => $endDate]) }}" class="btn btn-primary mt-4 w-100">Book Now</a>
            @endisset
        @endauth
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Information</div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                                <td>Room Type:</td>
                                <td>{{ $roomType->name }}</td>
                            </tr>
                            <tr>
                                <td>Price:</td>
                                <td>RM {{ $roomType->price }}</td>
                            </tr>
                            <tr>
                                <td>Single Bed:</td>
                                <td>{{ $singleBed }}</td>
                            </tr>
                            <tr>
                                <td>Double Bed:</td>
                                <td>{{ $doubleBed }}</td>
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
                            @isset($count)
                                <tr>
                                    <td>Available:</td>
                                    <td>
                                        {{ $count }}  {{ Str::plural("room", $count) }}
                                    </td>
                                </tr>
                            @endisset
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
