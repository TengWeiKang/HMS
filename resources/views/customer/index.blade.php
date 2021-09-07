@extends('customer.layouts.template')

@section('title')
    Hotel Booking
@endsection

@section('content')
    <section class="accomodation_area section_gap">
        <div class="container">
            <div class="section_title text-center">
                <h2 class="title_color">Hotel Accomodation</h2>
                <p>We all live in an age that belongs to the young at heart. Life that is becoming extremely fast, </p>
            </div>
            <div class="row mb_30" id="accomodation">
                <div class="col-lg-3 col-sm-6">
                    <div class="accomodation_item text-center">
                        <div class="hotel_img">
                            <img src="{{ asset("customer/image/room1.jpg") }}" alt="">
                            @auth("customer")
                                <a href="#" class="btn theme_btn button_hover">Book Now</a>
                            @endauth
                        </div>
                        <a href="#"><h4 class="sec_h4">Double Deluxe Room</h4></a>
                        <h5>$250<small>/night</small></h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push("script")

@endpush
