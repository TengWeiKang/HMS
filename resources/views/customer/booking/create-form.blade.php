@extends("customer.layouts.template")

@push("css")
<link rel="stylesheet" href="{{ asset("customer/vendors/fullcalendar/css/fullcalendar.min.css") }}">
<style>
    .fc-toolbar h2 {
        text-transform: initial;
    }
    .fc-basic-view .fc-body .fc-row {
        min-height: 2.5rem;
    }
    .fc-day-number {
        float: none !important;
        margin: 0 auto !important;
    }
    .fc {
        text-align: center !important;
    }
    .bg-red {
        background-color: red;
    }
    #calendar h2 {
        font-size: 16px;
    }
    .fc-unthemed td.fc-today {
        background: rgba(0, 255, 50, 0.3)
    }
    .fc-highlight {
        background: #aaa;
        opacity: .5;
    }
</style>
@endpush

@php
    $user = Auth::user();
@endphp

@section("title")
    Hotel Booking | {{ $roomType->name }}
@endsection

@section("title2")
    {{ $roomType->name }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if (session('message'))
                <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <div class="row">
                    <div class="col-4">
                        <div class="accomodation_item mb-0">
                            <div class="hotel_img text-center border border-secondary">
                                <img src="{{ $roomType->imageSrc() }}" alt="Hotel PlaceHolder">
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td width="30%">Room Type:</td>
                                    <td>{{ $roomType->name }}</td>
                                </tr>
                                <tr>
                                    <td>Price <small>/night</small>:</td>
                                    <td>RM {{ number_format($roomType->price, 2) }}</td>
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
                                <tr>
                                    <td>Available:</td>
                                    <td>
                                        {{ $count }}  {{ Str::plural("room", $count) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-title">Booking Form</div>
                <hr>
                <form id="booking-form" action="{{ route("customer.booking.create", ["roomType" => $roomType, "singleBed" => $singleBed, "doubleBed" => $doubleBed, "startDate" => $startDate, "endDate" => $endDate]) }}" method="POST">
                    @csrf
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-6 pl-lg-0">
                            <label for="firstName">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("firstName") border-danger @enderror" id="firstName" name="firstName" min="0" step="1" placeholder="First Name" value="{{ old("firstName", $user->first_name) }}">
                            @error("firstName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 pr-lg-0">
                            <label for="lastName">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("lastName") border-danger @enderror" id="lastName" name="lastName" min="0" step="1" placeholder="Last Name" value="{{ old("lastName", $user->last_name) }}">
                            @error("lastName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="phone">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("phone") border-danger @enderror" name="phone" placeholder="Contact Number (E.g. 012-3456789)" value="{{ old("phone", $user->phone) }}">
                        @error("phone")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Booking Date</label>
                        <div class="col-lg-4 pl-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("startDate") border-danger @enderror" id="startDate" name="startDate" data-prev="" value="{{ old("startDate", $startDate) }}">
                            @error("startDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-1 text-center my-lg-auto">TO</label>
                        <div class="col-lg-4 pr-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("endDate") border-danger @enderror" id="endDate" name="endDate" data-prev="" value="{{ old("endDate", $endDate) }}">
                            @error("endDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-3 text-center my-lg-auto h6"><span id="numDays">0</span> night(s)</label>
                    </div>
                    @error('dateConflict')
                        <div class="col-lg-12 pl-lg-0">
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        </div>
                    @enderror
                    <div class="form-group col-12 mt-3">
                        <label class="h6">Booking Price: RM <span id="totalPrice">0.00</span></label>
                    </div>
                    <div class="form-group col-12 mt-3">
                        <input type="hidden" name="deposit" value="100">
                        <label class="h6">Deposit: RM {{ number_format(App\Models\Reservation::DEPOSIT, 2) }}</label>
                    </div>
                    <hr>
                    <label for="">Bank Information</label>
                    <div class="form-group row mx-2">
                        <div class="col-12">
                            <label for="cardNumber">Card Number</label>
                            <input type="number" id="cardNumber" name="cardNumber" class="form-control" placeholder="Card Number" required>
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <div class="col-6">
                            <label for="expiredDate">Expired Date</label>
                            <input type="month" class="form-control" name="expiredDate" placeholder="Card Number" min="{{ date("Y-m") }}" required>
                        </div>
                        <div class="col-6">
                            <label for="cvv">CVV/CVV2</label>
                            <input type="password" class="form-control" id="cvv" name="cvv" placeholder="Card Number" required>
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <div class="col-12">
                            <label for="">Payment Amount: RM 100.00 (For Deposit)</label>
                        </div>
                    </div>
                    <div class="form-group col-12 mt-4">
                        <button type="submit" class="btn btn-primary btn-round w-100"><i class="icon-plus"></i> Book Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Calendar <span id="loading" class="text-warning"></span></div>
                <hr>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset("customer/vendors/fullcalendar/js/moment.min.js") }}"></script>
<script src="{{ asset("customer/vendors/fullcalendar/js/fullcalendar.min.js") }}"></script>
<script>
    $(document).ready(function() {
        $('input[type="date"]').on('focusin', function(){
            $(this).data('prev', $(this).val());
        });

        function updateAndTriggerSwal(title, message) {
            let tempStartDate = $("#startDate").data("prev");
            let tempEndDate = $("#endDate").data("prev");
            if (tempStartDate === "" || tempEndDate === "") {
                $('#calendar').fullCalendar('unselect');
                $("#startDate")[0].value = tempStartDate;
                $("#endDate")[0].value = tempEndDate;
            }
            else {
                $('#calendar').fullCalendar('select', moment(tempStartDate), moment(tempEndDate).add(1, "days"));
            }
            Swal.fire({
                title: title,
                text: message,
                icon: "error",
            });
        }

        function changeDate() {
            let startDate = $("#startDate")[0].value;
            let endDate = $("#endDate")[0].value;
            if (startDate !== "" && endDate !== "") {
                startDate = new Date(startDate);
                endDate = new Date(endDate);
                let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24) + 1;
                $("#numDays")[0].innerHTML = numberOfDays;
                $("#totalPrice")[0].innerHTML = (numberOfDays * {{ $roomType->price }}).toFixed(2);
                $('#calendar').fullCalendar('select', moment(startDate), moment(endDate).add(1, "days"));
            }
            else {
                $("#numDays").html(0);
                $("#totalPrice").html("0.00");
            }
        }

        $("#cvv").on("input", function() {
            this.setCustomValidity("");
            this.value = this.value.substr(0, 3);
        });

        $("#cardNumber").on("input", function() {
            this.setCustomValidity("");
            this.value = this.value.substr(0, 16);
        });

        $("#booking-form").on("submit", function(e) {
            let cvv = $("#cvv")[0];
            let cardNumber = $("#cardNumber")[0];
            if (cvv.value.length != 3) {
                cvv.setCustomValidity("CVV must be exact 3 character");
                cardNumber.reportValidity();
                e.preventDefault();
            }
            if (cardNumber.value.length != 16) {
                cardNumber.setCustomValidity("Card number must be exact 16 number");
                cardNumber.reportValidity();
                e.preventDefault();
            }
        });

        $("#calendar").fullCalendar({
            selectable: false,
            unselectAuto: false,
            height: "auto",
            nowIndicator: true,
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            // eventSources: [{
            //     url: "{{ route("customer.booking.json") }}",
            //     type: "POST",
            //     data: {
            //         "_token": "{{ csrf_token() }}",
            //         "roomID": {{ $roomType->id }}
            //     }
            // }],
            // select: function(startDate, endDate) {
            //     let dateNow = moment().startOf('day');
            //     if (dateNow > startDate) {
            //         updateAndTriggerSwal("Invalid Date", "The starting date cannot be the passed date");
            //     }
            //     else if (dateNow > endDate) {
            //         updateAndTriggerSwal("Invalid Date", "The ending date cannot be the passed date");
            //     }
            //     else if (startDate >= endDate) {
            //         updateAndTriggerSwal("Invalid Date", "The starting date cannot be over than ending date")
            //     }
            //     else {
            //         let events = $("#calendar").fullCalendar("clientEvents");
            //         let isValid = true;
            //         endDate.subtract(1, "days");
            //         events.forEach(event => {
            //             event.end.subtract(1, "days");
            //             if (event.start <= startDate && event.end >= startDate || event.start <= endDate && event.end >= endDate || event.start >= startDate && event.end <= endDate)
            //             {
            //                 isValid = false;
            //             }
            //         });
            //         endDate.add(1, "days");
            //         if (isValid) {
            //             let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
            //             endDate.subtract(1, "days");
            //             $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
            //             $("#startDate").data('prev', startDate.format("YYYY-MM-DD"));
            //             $("#endDate")[0].value = endDate.format("YYYY-MM-DD");
            //             $("#endDate").data('prev', endDate.format("YYYY-MM-DD"));
            //             $("#numDays")[0].innerHTML = numberOfDays;
            //             $("#totalPrice")[0].innerHTML = (numberOfDays * {{ $roomType->price }}).toFixed(2);
            //         }
            //         else {
            //             updateAndTriggerSwal("Date Conflict", "The booking date has conflict with other booking");
            //         }
            //     }
            // },
        });
        $("#calendar").fullCalendar("gotoDate", "{{ $startDate }}")
        $("#startDate, #endDate").change(function() {
            changeDate();
        })
        changeDate();
    });
</script>
@endpush
