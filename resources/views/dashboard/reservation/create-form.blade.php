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
    .card .table td {
        padding-left: .5rem;
        padding-right: .5rem;
    }
</style>
@endpush

@section("title")
    Dashboard | New Reservation
@endsection

@section("content")
<div class="row mt-3">
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
                <form id="reservation-form" action="{{ route("dashboard.reservation.create") }}" method="POST">
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
                                <input type="checkbox" id="checkIn"/>
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
                    <div class="form-group row mx-2">
                        <label for="rooms">Add Room <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="rooms"></select>
                    </div>
                    <hr style="border-width: 4px">
                    <div id="add-rooms">
                        <div class="form-group row mx-2">
                            <label for="room">Added Room <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <hr style="border-width: 2px">
                    <div class="form-group row mx-2">
                        <label for="passport">NRIC / Passport (Able to custom input) <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="passport" name="passport">
                            @foreach ($customers as $customer)
                                <option value="c||{{ $customer->id }}" data-phone="{{ $customer->phone }}" data-first-name="{{ $customer->first_name }}" data-last-name="{{ $customer->last_name }}" data-email="{{ $customer->email }}">{{ $customer->passport }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("email") border-danger @enderror" id="email" name="email" placeholder="Email" value="{{ old("email") }}">
                        @error("email")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-6 pl-lg-0">
                            <label for="firstName">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("firstName") border-danger @enderror" id="firstName" name="firstName" placeholder="First Name" value="{{ old("firstName") }}">
                            @error("firstName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 pr-lg-0">
                            <label for="lastName">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-rounded @error("lastName") border-danger @enderror" id="lastName" name="lastName" placeholder="Last Name" value="{{ old("lastName") }}">
                            @error("lastName")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
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
                    <div class="form-group col-12 mt-4">
                        <label class="h5">Booking Price: RM <span id="totalPrice">0.00</span></label>
                    </div>
                    <div class="form-group col-12">
                        <label class="h5">Deposit: RM <span id="deposit">0.00</span></label>
                        <input type="hidden" name="deposit">
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
    </div>
</div>
@endsection

@push("script")
<script src="{{ asset("dashboard/plugins/fullcalendar/js/moment.min.js") }}"></script>
<script src="{{ asset("dashboard/plugins/fullcalendar/js/fullcalendar.min.js") }}"></script>
<script>
    $(document).ready(function() {
        $('select.form-control#roomType').select2();
        $roomSelect = $('select.form-control#rooms');
        $roomSelect.select2({
            minimumResultsForSearch: -1,
            placeholder: "Please fill in reservation date before choose a room",
            ajax: {
                url: "{{ route("dashboard.reservation.search") }}",
                method: "POST",
                data: function(params) {
                    let ignoreID = $("input[name='room[]']").map(function(){return $(this).val();}).get();
                    return {
                        "_token": "{{ csrf_token() }}",
                        "single": $("#singleBed").val(),
                        "double": $("#doubleBed").val(),
                        "person": $("#person").val(),
                        "roomType": $("#roomType").val(),
                        "startDate": $("#startDate").val(),
                        "endDate": $("#endDate").val(),
                        "checkIn": $("#checkIn").prop("checked"),
                        "roomIgnoreID": ignoreID,
                    };
                },
            },
            templateResult: function(room) {
                if (room.id == undefined)
                    return room.text;
                let facilities = "";
                if (room.facilities.length != 0) {
                    facilities = room.facilities.join(", ");
                }
                else {
                    facilities = '<span class="select2-result-room__facilities-empty">No Facilities</span>';
                }
                html = `<div class="select2-result-room">
                            <div class="select2-result-room__title">
                                ` + room.text + `
                            </div>`;
                if (room.single_bed > 0) {
                    html += `<div class="select2-result-room__description">
                            <div class="select2-result-room__singlebed">
                                ` + room.single_bed + ` single bed
                            </div>`;
                }
                if (room.double_bed > 0) {
                    html += `<div class="select2-result-room__doublebed">
                                ` + room.double_bed + ` double bed
                            </div>`;
                }
                html += `<div class="select2-result-room__doublebed">
                            Facilities: ` + facilities + `
                        </div>
                    </div>
                </div>`;
                var $container = $(html);
                return $container;
            },
            templateSelection: function(room) {
                let isCheckIn = $("#checkIn").prop("checked");
                $("select#rooms").data("id", room.id);
                $("select#rooms").data("price", room.price);
                $("select#rooms").data("title", room.text);
                $("select#rooms").data("occupied", room.room_available);
                return "Please fill in reservation date before choose a room";
            },
        });
        $roomSelect.on("select2:select", function (e) {
            updateBookingPrice();
            let startDate = $("#startDate").val();
            let d = new Date(startDate);
            let today = new Date();
            d.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            let disable = true;
            if (today >= d) {
                disable = false;
            }

            $("#rooms").find("option").remove().trigger("change");
            let isCheckIn = $("#checkIn").prop("checked");
            let id = $(this).data("id");
            let title = $(this).data("title");
            let price = $(this).data("price");
            let isAvailable = $(this).data("occupied") == 1;
            let html = `
                <div class="div-room form-group row mx-2">
                    <div class="col-lg-10 pl-lg-0">
                        <input type="text" class="form-control form-control-rounded" value="` + title + `" readonly>
                        <input type="hidden" name="room[]" value="` + id + `" readonly>
                        <div name="price" data-price="` + price + `"></div>
                    </div>
                    <div class="col-lg-2 text-center">
                        <a class="delete-room-row" style="cursor: pointer; font-size: 20px">
                            <i class="zmdi zmdi-delete text-white"></i>
                        </a>
                    </div>
                </div>
                `;
            $("#add-rooms").append(html);
            updateBookingPrice();
            bindListener();
        });
        $roomSelect.on("select2:open", function (e) {
            $("#rooms").find("option").remove().trigger("change");
        });

        $passportSelect = $('select.form-control#passport');
        $passportSelect.select2({
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
        $passportSelect.on("select2:select", function (e) {
            updateCustomerInfo();
        });

        $("#startDate, #endDate, #checkIn").on("input", function (){
            resetRoomInput();
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

        function disableCheckIn() {
            let startDate = $("#startDate").val();
            if (startDate != "") {
                let d = new Date(startDate);
                d.setHours(0, 0, 0, 0);
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                if (today >= d) {
                    $("#checkIn").prop("disabled", false);
                }
                else {
                    $("#checkIn").prop("disabled", "disabled");
                    $("#checkIn").prop("checked", false);
                }
            }
        }

        function bindListener() {
            $(".delete-room-row").unbind();
            $(".delete-room-row").on("click", function () {
                $(this).parents(".div-room").remove();
                updateBookingPrice();
            });
        }

        function updateCustomerInfo() {
            let phone = $("#passport").find(":selected").data("phone") ?? "";
            let firstName = $("#passport").find(":selected").data("first-name") ?? "";
            let lastName = $("#passport").find(":selected").data("last-name") ?? "";
            let email = $("#passport").find(":selected").data("email") ?? "";
            $("#phone").val(phone);
            $("#firstName").val(firstName);
            $("#lastName").val(lastName);
            $("#email").val(email);
        }

        function updateAndTriggerSwal(title, message) {
            $('#calendar').fullCalendar('unselect');
            $("#startDate")[0].value = "";
            $("#endDate")[0].value = "";
            $("#checkIn").prop("disabled", "disabled");
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
                let bookingPrice = 0;
                $data = $("div[name='price']");
                $data.each(function (index, element) {
                    bookingPrice += parseFloat($(element).data("price"));
                })
                startDate = moment(startDate);
                endDate = moment(endDate);
                let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24) + 1;
                $("#numDays")[0].innerHTML = numberOfDays;
                $("#totalPrice")[0].innerHTML = (numberOfDays * bookingPrice).toFixed(2);
                let deposit = $data.length * {{ App\Models\Reservation::DEPOSIT }};
                $("#deposit").html(parseFloat(deposit).toFixed(2));
                $("input[name='deposit']").val(deposit);
            }
        }

        function resetRoomInput() {
            $(".div-room").remove();
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
                    disableCheckIn();
                    resetRoomInput();
                    $("#calendar").fullCalendar("removeEventSources");
                }
            },
        });
        $("#reservation-form").on("submit", function(e) {
            if ($("input[name='room[]']").length <= 0) {
                e.preventDefault();
                    Swal.fire({
                    title: "Missing Information",
                    text: "Please add at least one room",
                    icon: "error",
                });
            }
        });
        changeDate();
        updateCustomerInfo();
        bindListener();
        disableCheckIn();
    });
</script>
@endpush
