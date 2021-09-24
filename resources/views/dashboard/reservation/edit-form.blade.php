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
    .bg-red {
        background-color: red;
    }
    .fc-highlight {
        background: #aaa;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #eee;
    }
    .card .table td {
        padding-left: .5rem;
        padding-right: .5rem;
    }
</style>
@endpush

@section("title")
    Dashboard | Edit Reservation
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Edit Reservation</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                @error('reserved')
                    <div class="text-danger text-center">{{ $message }}</div>
                @enderror
                <hr>
                <form action="{{ route("dashboard.reservation.edit", ["reservation" => $reservation]) }}" method="POST">
                    @csrf
                    @method("PUT")
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
                                <input type="checkbox" id="checkIn" name="checkIn" @if ($reservation->check_in != null) checked @endif/>
                                <label for="checkIn">Check In</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Reservation Date <span class="text-danger">*</span></label>
                        <div class="col-lg-4 pl-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("startDate") border-danger @enderror" id="startDate" name="startDate" data-prev="" value="{{ old("startDate", $reservation->start_date->format("Y-m-d")) }}" required>
                        </div>
                        <label class="col-lg-1 text-center my-lg-auto">TO</label>
                        <div class="col-lg-4 pr-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("endDate") border-danger @enderror" id="endDate" name="endDate" data-prev="" value="{{ old("endDate", $reservation->end_date->format("Y-m-d")) }}" required>
                        </div>
                        <label class="col-lg-3 text-center my-lg-auto h6"><span id="numDays">{{ $reservation->dateDifference() }}</span> night(s)</label>
                    </div>
                    @if ($errors->hasAny(["startDate", "endDate"]))
                        <div class="col-lg-6 pl-lg-0">
                            <div class="ml-2 text-sm text-danger">
                                @error('startDate')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 pr-lg-0">
                            <div class="ml-2 text-sm text-danger">
                                @error('endDate')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    @endif
                    @error('dateConflict')
                        <div class="col-lg-12 pl-lg-0">
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        </div>
                    @enderror
                    <div class="form-group row mx-2">
                        <label for="roomId">Room <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="rooms" name="roomId" required>
                            <option value="{{ $reservation->room->id }}" data-room-id="{{ $reservation->room->room_id }}" data-price="{{ $reservation->room->type->price }}">{{ $reservation->room->room_id }} - {{ $reservation->room->name }} ({{ $reservation->room->statusName(false) }})</option>
                        </select>
                        @error("room")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row mx-2">
                        <label for="customer">Customer (Able to custom input) <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="customers" name="customer">
                            @foreach ($customers as $customer)
                                <option value="c||{{ $customer->id }}" data-phone="{{ $customer->phone }}" @if ($reservation->reservable instanceof App\Models\Customer && $customer->id == $reservation->reservable->id) selected @endif>{{ $customer->username }}</option>
                            @endforeach
                            @if ($reservation->reservable instanceof App\Models\Guest)
                                <option value="g||{{ $reservation->reservable->username }}" data-phone="{{ $reservation->reservable->phone }}" selected>{{ $reservation->reservable->username }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="phone">Contact Number (E.g. 012-3456789) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("phone") border-danger @enderror" id="phone" name="phone" placeholder="Contact Number" value="{{ old("phone", $reservation->reservable->phone) }}" @if ($reservation->reservable instanceof App\Models\Customer) readonly @endif>
                    </div>
                    <div class="form-group col-12 mt-5">
                        <label class="h5">Booking Price: RM <span id="totalPrice">{{ number_format($reservation->bookingPrice(), 2) }}</span></label>
                    </div>
                    @if ($reservation->check_in != null && $reservation->check_out == null)
                        <div class="form-group col-12 mt-3">
                            <a href="{{ route("dashboard.reservation.service", ["reservation" => $reservation]) }}" class="btn btn-primary btn-round px-5"><i class="icon-plus"></i> Add Room Service</a>
                        </div>
                    @endif
                    <div class="form-group col-12 mt-4">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-pencil"></i> Update</button>
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
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                                <td width="10%">Room ID:</td>
                                <td id="room-id">{{ $reservation->room->room_id }}</td>
                            </tr>
                            <tr>
                                <td>Room Name:</td>
                                <td id="room-name">{{ $reservation->room->name }}</td>
                            </tr>
                            <tr>
                                <td>Room Type:</td>
                                <td id="room-type">{{ $reservation->room->type->name }}</td>
                            </tr>
                            <tr>
                                <td>Price:</td>
                                <td id="room-price">RM {{ number_format($reservation->room->type->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Single Bed:</td>
                                <td id="room-single-bed">{{ $reservation->room->type->single_bed }}</td>
                            </tr>
                            <tr>
                                <td>Double Bed:</td>
                                <td id="room-double-bed">{{ $reservation->room->type->double_bed }}</td>
                            </tr>
                            <tr>
                                <td>Facilities:</td>
                                <td id="room-facilities">
                                    @forelse ($reservation->room->type->facilities->pluck("name")->toArray() as $facility)
                                        {{ $facility }}<br>
                                    @empty
                                        <span style="color: #F33">No Facilities</span>
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

@push("script")
<script src="{{ asset("dashboard/plugins/fullcalendar/js/moment.min.js") }}"></script>
<script src="{{ asset("dashboard/plugins/fullcalendar/js/fullcalendar.min.js") }}"></script>
<script>
    $(document).ready(function() {
        var isInitialize = true;
        $('select.form-control#roomType').select2();
        $roomSelect = $('select.form-control#rooms');
        $roomSelect.select2({
            minimumResultsForSearch: -1,
            placeholder: "Please fill in reservation date before select a room",
            ajax: {
                url: "{{ route("dashboard.reservation.search") }}",
                method: "POST",
                data: function(params) {
                    return {
                        "ignoreID": {{ $reservation->id }},
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
                if (container.text == "") {
                    return container.text;
                }
                if (container.price === undefined) {
                    return container.text;
                }
                $(container.element).data("room-id", container.room_id);
                $(container.element).data("price", container.price);
                $("#room-id").html(container.room_id);
                $("#room-name").html(container.room_name);
                $("#room-type").html(container.room_type);
                $("#room-price").html("RM " + container.price.toFixed(2));
                $("#room-single-bed").html(container.single_bed);
                $("#room-double-bed").html(container.double_bed);
                if (container.facilities.length != 0) {
                    $("#room-facilities").html(container.facilities.join("<br>"));
                }
                else {
                    $("#room-facilities").html('<span style="color: #F33">No Facilities</span>');
                }
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
        $roomSelect.on("select2:open", function (e) {
            $("#rooms").find("option").remove().trigger("change");
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
            $selector = $(this).find(":selected")
            let phone = $selector.data("phone") || "";
            let isGuest = $selector.val().startsWith("g");
            if (phone == "" || isGuest) {
                $("#phone").prop("readonly", false);
            }
            else {
                $("#phone").prop('readonly', true);
            }
            $("#phone").val(phone);
        });
        $('.select2.select2-container').addClass('form-control form-control-rounded');

        $("#singleBed, #doubleBed, #person, #roomType, #startDate, #endDate, #checkIn").on("input", function (){
            resetRoomInput
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
                updateBookingPrice();
            }
        }

        function updateBookingPrice() {
            let startDate = $("#startDate")[0].value;
            let endDate = $("#endDate")[0].value;
            if (startDate != "" && endDate != "") {
                startDate = moment(startDate);
                endDate = moment(endDate);
                let price = $("#rooms").find(":selected").data("price") ?? 0;
                let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24) + 1;
                $("#numDays")[0].innerHTML = numberOfDays;
                $("#totalPrice")[0].innerHTML = (numberOfDays * price).toFixed(2);
            }
        }

        function resetRoomInput() {
            $("#rooms").val("").change();
            $("#totalPrice")[0].innerHTML = (0).toFixed(2);
            $("#room-id").html("");
            $("#room-name").html("");
            $("#room-type").html("");
            $("#room-price").html("");
            $("#room-single-bed").html("");
            $("#room-double-bed").html("");
            $("#room-facilities").html("");
            $("#calendar").fullCalendar("removeEventSources");
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
                let loadingSpan = $("#loading")[0];
                if (isLoading) {
                    loadingSpan.innerHTML = "(Fetching Data)";
                }
                else {
                    loadingSpan.innerHTML = "";
                }
            },
            select: function(startDate, endDate) {
                let dateNow = moment().startOf('day');
                if (startDate >= endDate) {
                    updateAndTriggerSwal("Invalid Date", "The starting date cannot be over than ending date")
                }
                else {
                    let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
                    endDate.subtract(1, "days");
                    $("#numDays").html(numberOfDays);
                    $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
                    $("#endDate")[0].value = endDate.format("YYYY-MM-DD");
                    if (!isInitialize) {
                        resetRoomInput();
                    }
                    isInitialize = false;
                    $("#calendar").fullCalendar("removeEventSources");
                }
            },
        });
        changeDate();
    });
</script>
@endpush
