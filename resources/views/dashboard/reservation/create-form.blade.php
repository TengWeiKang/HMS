@extends("dashboard.layouts.template")

@push("css")
<link href="{{ asset("dashboard/plugins/fullcalendar/css/fullcalendar.min.css") }}" rel="stylesheet"/>
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
    .fc-unthemed td.fc-today {
        background: rgba(0, 255, 50, 0.3)
    }
    .bg-red {
        background-color: red;
    }
    .fc-highlight {
        background: #aaa;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #eee;
    }
</style>
@endpush

@section("title")
    Dashboard | New Reservation
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Reservation Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                @if (session('error'))
                    <div class="text-danger text-center">{{ session('error') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.reservation.create") }}" method="POST">
                    @csrf
                    <div class="title font-weight-bold">Filter Room</div>
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-4 pl-lg-0">
                            <label for="singleBed">Single Bed</label>
                            <input type="number" class="form-control form-control-rounded" id="singleBed" name="singleBed" min="0" step="1" placeholder="Number of Single Bed" value="{{ old("singleBed") }}">
                        </div>
                        <div class="col-lg-4">
                            <label for="doubleBed">Double Bed</label>
                            <input type="number" class="form-control form-control-rounded" id="doubleBed" name="doubleBed" min="0" step="1" placeholder="Number of Double Bed" value="{{ old("doubleBed") }}">
                        </div>
                        <div class="col-lg-4 pr-lg-0">
                            <label for="person">Number of Person</label>
                            <input type="number" class="form-control form-control-rounded" id="person" name="person" min="0" step="1" placeholder="Number of Person" value="{{ old("person") }}">
                        </div>
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-8 pl-0">
                            <label for="roomType">Room Types</label>
                            <select class="form-control form-control-rounded" id="roomType" name="roomType">
                                <option value="">Any</option>
                                @foreach ($roomTypes as $roomType)
                                    @continue ($roomType->rooms->count() == 0)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 pr-0">
                            <label for="roomType">Check in now?</label>
                            <div class="icheck-material-white">
                                <input type="checkbox" id="checkIn" name="checkIn"/>
                                <label for="checkIn">Check In</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Reservation Date <span class="text-danger">*</span></label>
                        <div class="col-lg-4 pl-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("startDate") border-danger @enderror" id="startDate" name="startDate" data-prev="" value="{{ old("startDate", request()->start_date) }}">
                            @error("startDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-1 text-center my-lg-auto">TO</label>
                        <div class="col-lg-4 pr-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("endDate") border-danger @enderror" id="endDate" name="endDate" data-prev="" value="{{ old("endDate", request()->end_date) }}">
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
                    <hr>
                    <div class="form-group row mx-2">
                        <label for="room">Room <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="rooms" name="room">
                        </select>
                        @error("room")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row mx-2">
                        <label for="customers">Customer (Able to custom input) <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="customers" name="customer">
                            @foreach ($customers as $customer)
                                <option value="c||{{ $customer->id }}" data-phone="{{ $customer->phone }}">{{ $customer->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="phone">Contact Number (E.g. 012-3456789) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("phone") border-danger @enderror" id="phone" name="phone" placeholder="Contact Number" value="{{ old("phone") }}">
                        @error("phone")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-12 mt-5">
                        <label class="h5">Booking Price: RM <span id="totalPrice">0.00</span></label>
                    </div>
                    <div class="form-group col-12 mt-4">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Create</button>
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
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Info</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
<script src="{{ asset("dashboard/plugins/fullcalendar/js/moment.min.js") }}"></script>
<script src="{{ asset("dashboard/plugins/fullcalendar/js/fullcalendar.min.js") }}"></script>
<script>
    $(document).ready(function() {
        let phone = $("#customers").find(":selected").data("phone") ?? "";
        $("#phone").val(phone);
        if (phone != "") {
            $("#phone").prop('readonly', true);
        }
        $('select.form-control#roomType').select2();
        $roomSelect = $('select.form-control#rooms');
        $roomSelect.select2({
            minimumResultsForSearch: -1,
            placeholder: "Please fill in reservation date before select a room",
            ajax: {
                url: "{{ route("dashboard.reservation.search") }}",
                method: "GET",
                data: function(params) {
                    return {
                        "_token": "{{ csrf_token() }}",
                        "single": $("#singleBed").val(),
                        "double": $("#doubleBed").val(),
                        "person": $("#person").val(),
                        "roomType": $("#roomType").val(),
                        "startDate": $("#startDate").val(),
                        "endDate": $("#endDate").val(),
                        "checkIn": $("#checkIn").prop("checked"),
                    };
                },
            },
            templateSelection: function(container) {
                $(container.element).data("price", container.price);
                return container.text;
            }
        });
        $roomSelect.on("select2:select", function (e) {
            updateBookingPrice();
            let calendar = $("#calendar");
            let sources = {
                url: "{{ route("dashboard.reservation.json") }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "roomID": $("#rooms")[0].value
                }
            };
            calendar.fullCalendar("removeEventSources");
            calendar.fullCalendar("addEventSource", sources);
        });

        $customerSelect = $('select.form-control#customers');
        $customerSelect.select2({
            multiple: false,
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: "g||" + term,
                    text: term,
                    newTag: true
                }
            }
        });
        $customerSelect.on("select2:select", function (e) {
            let phone = $(this).find(":selected").data("phone") ?? "";
            if (phone == "") {
                $("#phone").prop("readonly", false);
            }
            else {
                $("#phone").prop('readonly', true);
            }
            $("#phone").val(phone);
        });

        $("#singleBed, #doubleBed, #person, #roomType, #startDate, #endDate, #checkIn").on("change", function (){
            $("#rooms").val("").change();
            $("#calendar").fullCalendar("removeEventSources");
        });

        $("#singleBed, #doubleBed").on("input", function(e) {
            let value1 = $("#singleBed").val();
            let value2 = $("#doubleBed").val();
            $personElement = $("#person");
            if (value1.length == 0 && value2.length == 0) {
                $personElement.prop("disabled", false);
            }
            else {
                $personElement.prop("disabled", true);
            }
        });
        $("#person").on("input", function(e) {
            let value = this.value;
            $bedElement = $("#singleBed, #doubleBed");
            if (value.length == 0) {
                $bedElement.prop("disabled", false);
            }
            else {
                $bedElement.prop("disabled", true);
            }
        });

        $("#startDate, #endDate").on("change", function() {
            changeDate();
        })

        $('.select2.select2-container').addClass('form-control form-control-rounded');

        // $("#rooms").change(function() {
        //     let calendar = $("#calendar");
        //     let sources = {
        //         url: "{{ route("dashboard.reservation.json") }}",
        //         type: "POST",
        //         data: {
        //             "_token": "{{ csrf_token() }}",
        //             "roomID": $("#rooms")[0].value
        //         }
        //     };
        //     // reset input
        //     $("#startDate")[0].value = "";
        //     $("#endDate")[0].value = "";
        //     $("#totalPrice")[0].innerHTML = "0.00";
        //     $("#numDays")[0].innerHTML = 0;
        //     calendar.fullCalendar("unselect");
        //     calendar.fullCalendar("removeEventSources");
        //     calendar.fullCalendar("addEventSource", sources);
        //     /*
        //     let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
        //     endDate.subtract(1, "days");
        //     $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
        //     $("#startDate").data('prev', startDate.format("YYYY-MM-DD"));
        //     $("#endDate")[0].value = endDate.format("YYYY-MM-DD");
        //     $("#endDate").data('prev', endDate.format("YYYY-MM-DD"));
        //     $("#numDays")[0].innerHTML = numberOfDays;
        //     let price = parseFloat($("#rooms").find(':selected').data('price'));
        //     $("#totalPrice")[0].innerHTML = (numberOfDays * price).toFixed(2);
        //     */
        // });

        function updateAndTriggerSwal(title, message) {
            $('#calendar').fullCalendar('unselect');
            $("#startDate")[0].value = "";
            $("#endDate")[0].value = "";
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
                $('#calendar').fullCalendar('select', moment(startDate), moment(endDate).add(1, "days"));
            }
        }

        function updateBookingPrice() {
            let startDate = $("#startDate")[0].value;
            let endDate = $("#endDate")[0].value;
            if (startDate != "" && endDate != "") {
                startDate = moment(startDate);
                endDate = moment(endDate);
                let price = $("#rooms").find(":selected").data("price") ?? 0;
                let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
                $("#numDays")[0].innerHTML = numberOfDays;
                $("#totalPrice")[0].innerHTML = (numberOfDays * price).toFixed(2);
            }
        }

        $("#calendar").fullCalendar({
            selectable: true,
            unselectAuto: false,
            height: "auto",
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            loading: function(isLoading, view) {
                let loadingSpan = $("#loading");
                if (isLoading) {
                    loadingSpan.html("(Fetching Data)");
                }
                else {
                    loadingSpan.html("");
                }
            },
            select: function(startDate, endDate) {
                let dateNow = moment().startOf('day');
                if (dateNow > startDate) {
                    updateAndTriggerSwal("Invalid Date", "The starting date cannot be the passed date");
                }
                else if (dateNow > endDate) {
                    updateAndTriggerSwal("Invalid Date", "The ending date cannot be the passed date");
                }
                else if (startDate >= endDate) {
                    updateAndTriggerSwal("Invalid Date", "The starting date cannot be over than ending date")
                }
                else {
                    let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
                    endDate.subtract(1, "days");
                    $("#numDays").html(numberOfDays);
                    $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
                    $("#endDate")[0].value = endDate.format("YYYY-MM-DD");
                    $("#rooms").val("").change();
                    $("#calendar").fullCalendar("removeEventSources");
                }
            },
        });
        changeDate();
    });
</script>
@endpush
