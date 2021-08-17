@extends("dashboard.layouts.template")

@push("css")
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
                        <label for="roomId">Room ID</label>
                        <select class="form-control form-control-rounded" id="rooms" name="roomId">
                            <option value="0">Admin</option>
                            <option value="1">Staff</option>
                            <option value="2">Housekeeper</option>
                        </select>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="customer">Customer</label>
                        <select class="form-control form-control-rounded" name="customer">
                            <option value="0">Admin</option>
                            <option value="1">Staff</option>
                            <option value="2">Housekeeper</option>
                        </select>
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Reservation Date</label>
                        <div class="col-lg-4 pl-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("startDate") border-danger @enderror" id="startDate" name="startDate" value="{{ old("startDate", date("Y-m-d")) }}">
                            @error("startDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-1 text-center my-lg-auto">TO</label>
                        <div class="col-lg-4 pr-lg-0">
                            <input type="date" class="form-control form-control-rounded @error("endDate") border-danger @enderror" id="endDate" name="endDate" value="{{ old("endDate", date("Y-m-d")) }}">
                            @error("endDate")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <label class="col-lg-3 text-center my-lg-auto h6"><span id="numDays">1</span> day(s)</label>
                    </div>
                    <div class="form-group col-12">
                        <div class="icheck-material-white">
                            <input type="checkbox" id="checkIn"/>
                            <label for="checkIn">Check In</label>
                        </div>
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
                <div class="card-title">Calendar</div>
                <hr>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function() {
            $('select.form-control').select2({
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
                            newTag: true // add additional parameters
                        }
                    }
                });
            $('.select2.select2-container').addClass('form-control form-control-rounded')
            $('.select2-selection--multiple').parents('.select2-container').addClass('form-select-multiple')

            function changeDate() {
                let startDate = moment($("#startDate")[0].value).format("YYYY-MM-DD");
                let endDate = moment($("#endDate")[0].value).add(1, "days").format("YYYY-MM-DD");
                // let dateNow = moment().startOf('day');
                // if (dateNow > startDate) {
                //     alert("The starting date cannot be the passed date");
                //     $("#startDate")[0].value = dateNow.format();
                // }
                // else if (dateNow > endDate) {
                //     alert("The ending date cannot be the passed date");
                //     $("#endDate")[0].value = startDate.format();
                // }
                // else if (startDate > endDate) {
                //     alert("The starting date cannot be over than ending date")
                //     $("#endDate")[0].value = startDate.format();
                // }
                // startDate = moment($("#startDate")[0].value).format("YYYY-MM-DD");
                // endDate = moment($("#endDate")[0].value).format("YYYY-MM-DD");
                $('#calendar').fullCalendar('select', startDate, endDate);
            }
            changeDate();

            $("#calendar").fullCalendar({
                selectable: true,
                unselectAuto: false,
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                select: function(startDate, endDate) {
                    let dateNow = moment().startOf('day');
                    let tempStartDate = moment($("#startDate")[0].value);
                    let tempEndDate = moment($("#endDate")[0].value).add(1, "days");
                    if (dateNow > startDate) {
                        alert("The starting date cannot be the passed date");
                        $('#calendar').fullCalendar('select', tempStartDate, moment.max(dateNow, tempEndDate));
                    }
                    else if (dateNow > endDate) {
                        alert("The ending date cannot be the passed date");
                        $('#calendar').fullCalendar('select', tempStartDate, moment.max(dateNow, tempEndDate));
                    }
                    else if (startDate > endDate) {
                        alert("The starting date cannot be over than ending date")
                        $('#calendar').fullCalendar('select', tempStartDate, moment.max(dateNow, tempEndDate));
                    }
                    else {
                        let numberOfDays = (endDate - startDate) / (1000 * 3600 * 24);
                        $("#startDate")[0].value = startDate.format("YYYY-MM-DD");
                        $("#endDate")[0].value = endDate.subtract(1, "days").format("YYYY-MM-DD");
                        $("#numDays")[0].innerHTML = numberOfDays;
                    }
                }
            });
            $("#startDate, #endDate").change(function() {
                changeDate();
            })
        });
    </script>
@endpush
