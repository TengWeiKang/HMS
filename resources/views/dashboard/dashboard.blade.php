@extends('dashboard.layouts.template')

@push('css')
<link href="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.css") }}" rel="stylesheet"/>
<style>
    .legend-color, .legend-text {
        height: 26px;
    }
    .legend-color {
        width: 26px;
    }
    .legend-text {
        width:auto;
    }
</style>
@endpush

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <div class="card-title">Calendar</div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div id="calendar"></div>
                </div>
            </div>
            <div class="row text-center mt-4" id="emptyRoom" style="display: none">
                <div class="col-lg-12">
                    <h6 class="text-center">No Room is detected. <a href="{{ route("dashboard.room.create") }}" style="color:blue; text-decoration: underline">Click Here to Add</a></h6>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.js") }}"></script>
<script>
        let calendarElement = document.getElementById("calendar");
        let calendar = new FullCalendar.Calendar(calendarElement, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            initialView: 'resourceTimelineTwoWeek',
            contentHeight: "auto",
            nowIndicator: true,
            eventOverlap: false,
            selectOverlap: false,
            displayEventTime: false,
            selectable: true,
            headerToolbar: {
                left: 'prev,today,next',
                center: 'title',
                right: 'resourceTimelineTwoWeek,resourceTimelineMonth,resourceTimelineYear'
            },
            views: {
                resourceTimelineTwoWeek: {
                    type: 'resourceTimeline',
                    duration: { weeks: 2 },
                    slotDuration: { days: 1 },
                    buttonText: '2 weeks',
                    dateIncrement: {weeks: 1}
                }
            },
            resourceAreaHeaderContent: 'Rooms',
            resources: {
                url: "{{ route("dashboard.json") }}",
                method: "POST",
                extraParams: {
                    "_token": "{{ csrf_token() }}",
                    "return": "resources"
                }
            },
            eventSources: [{
                url: "{{ route("dashboard.json") }}",
                method: "POST",
                extraParams: {
                    "_token": "{{ csrf_token() }}",
                    "return": "events"
                }
            }],
            eventSourceSuccess: function(rawEvents, xhr) {
                if (rawEvents.length == 0) {
                    $("#emptyRoom").removeAttr("style");
                }
            },
            select: function(selectionInfo) {
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                let startDate = selectionInfo.start;
                let endDate = selectionInfo.end;
                if (startDate < today || endDate < today) {
                    Swal.fire({
                        title: "Invalid Date",
                        text: "The date cannot be pass date",
                        icon: "error",
                    });
                }
                else {
                    const RESERVATION_CREATE_URL = "{{ route("dashboard.reservation.create") }}";
                    console.log(RESERVATION_CREATE_URL);
                    let startDateString = startDate.toISOString().split('T')[0];
                    let endDateString = endDate.toISOString().split('T')[0];
                    let roomID = selectionInfo.resource.id;
                    let params = $.param({
                        "room_id": roomID,
                        "start_date": startDateString,
                        "end_date": endDateString
                    });
                    let redirectURL = RESERVATION_CREATE_URL + "?" + params;
                    window.location.href = redirectURL;
                }
            }
        });
        calendar.render();
</script>
@endpush
