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
<!-- Confirmation Create Reservation Modal -->
<div class="modal fade" id="confirmation-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mx-2">
                    <h5>Room ID: <span id="room_id"></span></h5>
                </div>
                <div class="row mx-2">
                    <h5>Start Date: <span id="start_date"></span></h5>
                </div>
                <div class="row mx-2">
                    <h5>End Date: <span id="end_date"></span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="redirectURL">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="redirectBtn" class="btn btn-primary">Redirect</button>
            </div>
        </div>
    </div>
</div>
<!-- Confirmation Modify Reservation Modal -->
<div class="modal fade" id="modify-date-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Date Modified</h5>
            </div>
            <div class="modal-body">
                <div class="row mx-2">
                    <h5>Customer: <span id="customer"></span></h5>
                </div>
                <div id="room_changes_info" class="d-none">
                    <div class="row mx-2 mt-3">
                        <h5>Room (Before): <span id="before_room"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>Room (After): <span id="after_room"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>Prices Changes: <span id="modified_price"></span></h5>
                    </div>
                </div>
                <div id="date_change_info" class="d-none">
                    <div class="row mx-2 mt-3">
                        <h5>Start Date (Before): <span id="before_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date (Before): <span id="before_end_date"></span></h5>
                    </div>
                    <div class="row mx-2 mt-3">
                        <h5>Start Date (After): <span id="after_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date (After): <span id="after_end_date"></span></h5>
                    </div>
                </div>
                <div id="date_unchange_info" class="d-none">
                    <div class="row mx-2 mt-3">
                        <h5>Start Date: <span id="unchanged_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date: <span id="unchanged_end_date"></span></h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="undoBtn" class="btn btn-secondary" data-dismiss="modal">Undo</button>
                <button type="button" id="saveBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.js") }}"></script>
<script>
    $(document).ready(function () {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $("button#redirectBtn").on("click", function() {
            let redirectURL = $("input#redirectURL").val();
            window.location.href = redirectURL;
        })

        function properDateFormat(date) {
            return date.getDate() + " " + months[date.getMonth()] + " " + date.getFullYear();
        }

        function dateISOString(date) {
            return date.toLocaleDateString("sv");
        }

        function dateDifferenceInDays(startDate, endDate) {
            let start = startDate.getTime();
            let end = endDate.getTime();
            return (end - start) / (24*3600*1000) + 1;
        }

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
                    endDate.setDate(endDate.getDate() - 1);
                    const RESERVATION_CREATE_URL = "{{ route("dashboard.reservation.create") }}";
                    let properStartDateString = properDateFormat(startDate);
                    let properEndDateString = properDateFormat(endDate);

                    let startDateString = dateISOString(startDate);
                    let endDateString = dateISOString(endDate);
                    let id = selectionInfo.resource.id;
                    let room_id = selectionInfo.resource.extendedProps.room_id;
                    let params = $.param({
                        "room_id": id,
                        "start_date": startDateString,
                        "end_date": endDateString
                    });
                    let redirectURL = RESERVATION_CREATE_URL + "?" + params;
                    $("span#room_id").html(room_id);
                    $("span#start_date").html(properStartDateString);
                    $("span#end_date").html(properEndDateString);
                    $("input#redirectURL").val(redirectURL);

                    $("#confirmation-modal").modal("show");
                }
            },
            eventDrop: function(eventDropInfo) {
                let oldEvent = eventDropInfo.oldEvent;
                let event = eventDropInfo.event;

                let oldEventEnd = new Date(oldEvent.end.setDate(oldEvent.end.getDate() - 1));
                let eventEnd = new Date(event.end.setDate(event.end.getDate() - 1));

                let oldResourceInfo = oldEvent.getResources()[0];
                let newResourceInfo = event.getResources()[0];

                let oldRoomID = oldResourceInfo.extendedProps.room_id;
                let oldRoomPrice = oldResourceInfo.extendedProps.price;
                let newRoomID = newResourceInfo.extendedProps.room_id;
                let newRoomPrice = newResourceInfo.extendedProps.price;
                if (oldRoomID != newRoomID) {
                    $("#room_changes_info").removeClass("d-none");
                    $("#before_room").html(oldRoomID + " (RM " + oldRoomPrice.toFixed(2) +" per night)");
                    $("#after_room").html(newRoomID + " (RM " + newRoomPrice.toFixed(2) +" per night)");
                    let oldTotalPrice = oldRoomPrice * dateDifferenceInDays(oldEvent.start, oldEventEnd);
                    let newTotalPrice = newRoomPrice * dateDifferenceInDays(event.start, eventEnd);
                    $("#modified_price").html("RM " + oldTotalPrice.toFixed(2) + " → RM " + newTotalPrice.toFixed(2));
                }
                else {
                    $("#room_changes_info").addClass("d-none");
                }
                let delta = eventDropInfo.delta;
                if (delta.days == 0 && delta.months == 0 && delta.years == 0) {
                    $("#date_change_info").addClass("d-none");
                    $("#date_unchange_info").removeClass("d-none");
                    $("#unchanged_start_date").html(properDateFormat(eventDropInfo.oldEvent.start));
                    $("#unchanged_end_date").html(properDateFormat(oldEventEnd));
                }
                else {
                    $("#date_unchange_info").addClass("d-none");
                    $("#date_change_info").removeClass("d-none");
                    $("#customer").html(event.title + " (" + dateDifferenceInDays(oldEvent.start, oldEventEnd) + " nights)");
                    $("#before_start_date").html(properDateFormat(eventDropInfo.oldEvent.start));
                    $("#before_end_date").html(properDateFormat(oldEventEnd));
                    $("#after_start_date").html(properDateFormat(event.start));
                    $("#after_end_date").html(properDateFormat(eventEnd));
                }
                $("#modify-date-modal").modal("show");
                $("#undoBtn").on("click", function() {
                    eventDropInfo.revert();
                });
                $("#saveBtn").on("click", function() {
                    let eventID = event.id;
                    let roomID = newResourceInfo.id;
                    let startDateISO = dateISOString(event.start);
                    let endDateISO = dateISOString(event.end);
                    //^ TODO: ajax post request
                });
            }
        });
        calendar.render();
    });
</script>
@endpush
