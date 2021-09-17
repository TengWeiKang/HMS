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
                @include("customer.components.accomodations")
            </div>
        </div>
    </section>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $("#person").on("input", function(e) {
                let value = this.value;
                $bedElement = $("#single, #double");
                if (value.length == 0) {
                    $bedElement.prop("disabled", false);
                    $("#single").prop("placeholder", "Single Bed")
                    $("#double").prop("placeholder", "Double Bed")
                }
                else {
                    $bedElement.prop("disabled", true);
                    $("#single").prop("placeholder", "Single Bed (disabled)")
                    $("#double").prop("placeholder", "Double Bed (disabled)")
                }
            });
            $("#single, #double").on("input", function(e) {
                let value1 = $("#single").val();
                let value2 = $("#double").val();
                $personElement = $("#person");
                if (value1.length == 0 && value2.length == 0) {
                    $personElement.prop("disabled", false);
                    $personElement.prop("placeholder", "Number of Person")
                }
                else {
                    $personElement.prop("disabled", true);
                    $personElement.prop("placeholder", "Number of Person (disabled)")
                }
            });
            $("#search-btn").on("click", function (e) {
                e.preventDefault();
                let arrival = $("#arrival").val();
                let departure = $("#departure").val();
                let single = $("#single").val();
                let double = $("#double").val();
                let roomType = $("#roomType").val();
                let person = $("#person").val();
                if (arrival != "" && departure != "" && new Date(arrival) > new Date(departure)) {
                    Swal.fire({
                        title: "Invalid Date",
                        text: "Arrival date must be earlier than departure date",
                        icon: "error",
                    });
                }
                else {
                    $.ajax({
                        type: "GET",
                        url: "{{ route("customer.search") }}",
                        datatype: "text",
                        data: {
                            "arrival": arrival,
                            "departure": departure,
                            "single": single,
                            "double": double,
                            "roomType": roomType,
                            "person": person,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            $("#accomodation").html(response);
                        }
                    });
                }
            });
        });
    </script>
@endpush
