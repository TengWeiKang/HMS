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
                <hr>
                <form action="{{ route("dashboard.reservation.create") }}" method="POST">
                    @csrf
                    <div class="form-group row mx-2">
                        <label for="roomId">Room</label>
                        <select class="form-control form-control-rounded" id="rooms" name="roomId">
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" data-price="{{ $room->price }}">{{ $room->room_id . " - " . $room->name . " (RM " . number_format($room->price, 2) . ")"}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="customer">Customer (Able to custom input)</label>
                        <select class="form-control form-control-rounded" id="customers" name="customer">
                            @foreach ($customers as $customer)
                                <option value="c||{{ $customer->id }}">{{ $customer->username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Reservation Date</label>
                        <div class="col-lg-4 pl-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("startDate") border-danger @enderror" id="startDate" name="startDate" data-prev="" value="{{ old("startDate") }}">
                            @error("startDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-1 text-center my-lg-auto">TO</label>
                        <div class="col-lg-4 pr-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("endDate") border-danger @enderror" id="endDate" name="endDate" data-prev="" value="{{ old("endDate") }}">
                            @error("endDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-3 text-center my-lg-auto h6"><span id="numDays">0</span> night(s)</label>
                    </div>
                    @if ($errors->has(["startDate", "endDate"]))
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
                                @error('dateConflict')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    @enderror
                    <div class="form-group col-12 mt-3">
                        <div class="icheck-material-white">
                            <input type="checkbox" id="checkIn" name="checkIn"/>
                            <label for="checkIn">Check In</label>
                        </div>
                    </div>
                    <div class="form-group col-12 mt-3">
                        <label class="h5">RM <span id="totalPrice">0.00</span></label>
                    </div>
                    <div class="form-group row mx-2 mt-4">
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
        $('select.form-control#rooms').select2();
        $('select.form-control#customers').select2({
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
        $('.select2.select2-container').addClass('form-control form-control-rounded');
        $('.select2-selection--multiple').parents('.select2-container').addClass('form-select-multiple');

        $('input[type="date"]').on('focusin', function(){
            $(this).data('prev', $(this).val());
        });

        $("#rooms").change(function() {
            let calendar = $("#calendar");
            let sources = {
                url: "{{ route("dashboard.reservation.json") }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "roomID": $("#rooms")[0].value
                }
            };
            // reset input
            $("#startDate")[0].value = "";
            $("#endDate")[0].value = "";
            $("#totalPrice")[0].innerHTML = "0.00";
            $("#numDays")[0].innerHTML = 0;
            calendar.fullCalendar("unselect");
            calendar.fullCalendar("removeEventSources");
            calendar.fullCalendar("addEventSource", sources);
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
                $('#calendar').fullCalendar('select', moment(startDate), moment(endDate).add(1, "days"));
            }
        }

        $("#calendar").fullCalendar({
            selectable: true,
            unselectAuto: false,
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
            eventSources: [{
                url: "{{ route("dashboard.reservation.json") }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "roomID": $("#rooms")[0].value
                }
            }],
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
                    let events = $("#calendar").fullCalendar("clientEvents");
                    let isValid = true;
                    endDate.subtract(1, "days");
                    events.forEach(event => {
                        event.end.subtract(1, "days");
                        if (event.start <= startDate && event.end >= startDate || event.start <= endDate && event.end >= endDate || event.start >= startDate && event.end <= endDate)
                        {
                            isValid = false;
                        }
                    });
                    endDate.add(1, "days");
                    if (isValid) {
                        let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
                        endDate.subtract(1, "days");
                        $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
                        $("#startDate").data('prev', startDate.format("YYYY-MM-DD"));
                        $("#endDate")[0].value = endDate.format("YYYY-MM-DD");
                        $("#endDate").data('prev', endDate.format("YYYY-MM-DD"));
                        $("#numDays")[0].innerHTML = numberOfDays;
                        let price = parseFloat($("#rooms").find(':selected').data('price'));
                        $("#totalPrice")[0].innerHTML = (numberOfDays * price).toFixed(2);
                    }
                    else {
                        updateAndTriggerSwal("Date Conflict", "The booking date has conflict with other booking");
                    }
                }
            }
        });
        $("#startDate, #endDate").change(function() {
            changeDate();
        })
    });
</script>
@endpush
