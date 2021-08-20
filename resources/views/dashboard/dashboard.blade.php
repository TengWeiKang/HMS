@extends('dashboard.layouts.template')

@push('css')
<link href="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.css") }}" rel="stylesheet"/>
@endpush

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <div class="card-title">Calendar <span id="loading" class="text-warning"></span></div>
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
            initialView: 'resourceTimelineMonth',
            contentHeight: "auto",
            nowIndicator: true,
            eventOverlap: false,
            displayEventTime: false,
            headerToolbar: {
                left: 'prevYear,prev,today,next,nextYear',
                center: 'title',
                right: 'resourceTimelineMonth,resourceTimelineYear'
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
        });
        calendar.render();
</script>
@endpush
