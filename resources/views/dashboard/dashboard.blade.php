@extends('dashboard.layouts.template')

@push('css')
<link href="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.css") }}" rel="stylesheet"/>
<style>
    .modal table td {
        color: black;
        padding-top: .25rem;
        padding-bottom: .25rem;
    }
    .event-pointer {
        cursor: pointer;
    }
    .icheck-material-white>input:first-child:checked+input[type=hidden]+label::before,
    .icheck-material-white>input:first-child:checked+label::before,
    [class*=icheck-material]>input:first-child+input[type=hidden]+label::before,
    [class*=icheck-material]>input:first-child+label::before {
        border-color: gray;
    }
    .c-mx {
        padding-left: 24.5px;
        padding-right: 24.5px;
    }
</style>
@endpush

@section('title')
    Dashboard
@endsection

@section('content')
<div class="card mt-3">
    <div class="card-header" style="font-size: 20px">Calendar <span id="loading" class="text-warning font-weight-normal small"></span>
        <div class="card-action">
            <a id="refetch-event" href="javascript:void();"><i class="icon-refresh text-white mr-1" style="font-size: 20px"></i></a>
        </div>
    </div>
    <div class="card-body">
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
<!-- Drag / Drop / Resize Event Modal -->
<div class="modal fade" id="drag-drop-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Date Modified</h5>
            </div>
            <div class="modal-body">
                <div class="row mx-2">
                    <h5>Customer: <span id="customer"></span></h5>
                </div>
                <div id="room_info" class="d-none mt-3">
                    <div class="row mx-2">
                        <h5>Room (Before): <span id="room_description"></span></h5>
                    </div>
                </div>
                <div id="room_changes_info" class="d-none mt-3">
                    <div class="row mx-2">
                        <h5>Room (Before): <span id="before_room"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>Room (After): <span id="after_room"></span></h5>
                    </div>
                </div>
                <div id="date_change_info" class="d-none mt-3">
                    <div class="row mx-2">
                        <h5>Start Date (Before): <span id="before_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date (Before): <span id="before_end_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>Start Date (After): <span id="after_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date (After): <span id="after_end_date"></span></h5>
                    </div>
                </div>
                <div id="date_unchange_info" class="d-none mt-3">
                    <div class="row mx-2">
                        <h5>Start Date: <span id="unchanged_start_date"></span></h5>
                    </div>
                    <div class="row mx-2">
                        <h5>End Date: <span id="unchanged_end_date"></span></h5>
                    </div>
                </div>
                <div id="price_changes_info" class="d-none mt-3">
                    <div class="row mx-2">
                        <h5>Booking Prices Changes: <span id="modified_price"></span></h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="undoBtn" class="btn btn-secondary" data-dismiss="modal">Undo</button>
                <button type="button" id="saveBtn" class="btn btn-primary" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Display Reservation Modal -->
<div class="modal fade" id="display-modal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reservation Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive col-12">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Room ID:</td>
                                    <td id="display-room-id"></td>
                                </tr>
                                <tr>
                                    <td>Customer:</td>
                                    <td id="display-customer"></td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td id="display-date"></td>
                                </tr>
                                <tr>
                                    <td>Total Nights:</td>
                                    <td id="display-total-night"></td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td id="display-total-price"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row c-mx">
                    <div class="icheck-material-secondary">
                        <input type="checkbox" id="newTab" name="newTab"/>
                        <label for="newTab">Open in New Tab</label>
                    </div>
                </div>
                <div id="display-check-in" class="row mt-3 d-none">
                    <div class="col-4">
                        <button type="button" class="btn btn-secondary w-100" name="check-in"><i class="fa fa-download text-white"></i> Check In</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-secondary w-100" name="edit"><i class="zmdi zmdi-edit text-white"></i> Edit</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-secondary w-100" name="view"><i class="zmdi zmdi-eye text-white"></i> View</button>
                    </div>
                </div>
                <div id="display-reserved" class="d-none">
                    <div class="row mt-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100" name="check-out"><i class="zmdi zmdi-check text-white"></i> Check Out</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100" name="add-service"><i class="zmdi zmdi-plus text-white"></i> Add Service</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100" name="edit"><i class="zmdi zmdi-edit text-white"></i> Edit</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100" name="view"><i class="zmdi zmdi-eye text-white"></i> View</button>
                        </div>
                    </div>
                </div>
                <div id="display-complete" class="row mt-3 d-none">
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary w-100" name="view"><i class="zmdi zmdi-eye text-white"></i> View</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary w-100" name="payment"><i class="zmdi zmdi-eye text-white"></i> Payment</button>
                    </div>
                </div>
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
        });


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
            lazyFetching: false,
            selectable: true,
            slotLabelFormat: [
                {day: 'numeric', month: 'numeric'},
            ],
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
            loading: function(isLoading) {
                let loadingSpan = $("#loading");
                if (isLoading) {
                    loadingSpan.html("(Fetching Data)");
                }
                else {
                    loadingSpan.html("");
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
            eventClick: function (eventClickInfo) {
                let event = eventClickInfo.event;
                let endDate = new Date(event.end.setDate(event.end.getDate() - 1));
                let dateDiff = dateDifferenceInDays(event.start, endDate);
                $("#display-room-id").html(event.getResources()[0].extendedProps.room_id);
                $("#display-customer").html(event.title);
                $("#display-date").html(properDateFormat(event.start) + " - " + properDateFormat(endDate));
                $("#display-total-night").html( dateDiff + " nights");
                $("#display-total-price").html("RM " + event.extendedProps.totalPrice.toFixed(2));

                $("button[name='check-in'], button[name='edit'], button[name='view'], button[name='check-out'], button[name='add-service'], button[name='payment']").unbind()

                let status = event.extendedProps.status;
                const CHECK_IN_URL = "{{ route("dashboard.reservation.check-in", ":reservationID") }}";
                const EDIT_URL = "{{ route("dashboard.reservation.edit", ":reservationID") }}";
                const VIEW_URL = "{{ route("dashboard.reservation.view", ":reservationID") }}";
                const ADD_SERVICE_URL = "{{ route("dashboard.reservation.service", ":reservationID") }}";
                const CHECK_OUT_URL = "{{ route("dashboard.payment.create", ":reservationID") }}";
                const PAYMENT_URL = "{{ route("dashboard.payment.view", ":paymentID") }}";
                let eventID = event.id;
                switch (status) {
                    case 0:
                        $("#display-check-in").removeClass("d-none");
                        $("#display-reserved, #display-complete").addClass("d-none");
                        $("button[name='check-in']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(CHECK_IN_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                        });
                        $("button[name='edit']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(EDIT_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                        });
                        break;
                    case 1:
                        $("#display-reserved").removeClass("d-none");
                        $("#display-check-in, #display-complete").addClass("d-none");
                        $("button[name='check-out']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(CHECK_OUT_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                        });
                        $("button[name='add-service']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(ADD_SERVICE_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                        });
                        $("button[name='edit']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(EDIT_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                        });
                        break;
                    case 2:
                        let paymentID = event.extendedProps.paymentId;
                        $("#display-complete").removeClass("d-none");
                        $("#display-check-in, #display-reserved").addClass("d-none");
                        $("button[name='payment']").on("click", function() {
                            let newTab = $("#newTab").prop("checked");
                            window.open(PAYMENT_URL.replace(":paymentID", paymentID), (newTab ? "_blank": "_self"));
                        });
                        break;
                }
                $("button[name='view']").on("click", function() {
                    let newTab = $("#newTab").prop("checked");
                    window.open(VIEW_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                });

                $("#display-modal").modal("show");
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
                    $("#room_changes_info, #price_changes_info, #room_info").removeClass("d-none");
                    $("#before_room").html(oldRoomID + " (RM " + oldRoomPrice.toFixed(2) +" per night)");
                    $("#after_room").html(newRoomID + " (RM " + newRoomPrice.toFixed(2) +" per night)");
                    let oldTotalPrice = oldRoomPrice * dateDifferenceInDays(oldEvent.start, oldEventEnd);
                    let newTotalPrice = newRoomPrice * dateDifferenceInDays(event.start, eventEnd);
                    $("#modified_price").html("RM " + oldTotalPrice.toFixed(2) + " → RM " + newTotalPrice.toFixed(2));
                }
                else {
                    $("#room_changes_info, #price_changes_info, #room_info").addClass("d-none");
                }
                let delta = eventDropInfo.delta;
                if (delta.days == 0 && delta.months == 0 && delta.years == 0) {
                    $("#date_unchange_info").removeClass("d-none");
                    $("#date_change_info").addClass("d-none");
                    $("#unchanged_start_date").html(properDateFormat(oldEvent.start));
                    $("#unchanged_end_date").html(properDateFormat(oldEventEnd));
                }
                else {
                    $("#date_unchange_info").addClass("d-none");
                    $("#date_change_info").removeClass("d-none");
                    $("#customer").html(event.title + " (" + dateDifferenceInDays(oldEvent.start, oldEventEnd) + " nights)");
                    $("#before_start_date").html(properDateFormat(oldEvent.start));
                    $("#before_end_date").html(properDateFormat(oldEventEnd));
                    $("#after_start_date").html(properDateFormat(event.start));
                    $("#after_end_date").html(properDateFormat(eventEnd));
                }
                $("#drag-drop-modal").modal("show");
                $("#undoBtn, #saveBtn").unbind();
                $("#undoBtn").on("click", function() {
                    eventDropInfo.revert();
                });
                $("#saveBtn").on("click", function() {
                    let eventID = event.id;
                    let roomID = newResourceInfo.id;
                    let startDateISO = dateISOString(event.start);
                    let endDateISO = dateISOString(eventEnd);
                    $.ajax({
                        type: "POST",
                        url: "{{ route("dashboard.reservation-update") }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: eventID,
                            room_id: roomID,
                            start_date: startDateISO,
                            end_date: endDateISO
                        }
                    });
                });
            },
            eventResize: function(eventResizeInfo) {
                let oldEvent = eventResizeInfo.oldEvent;
                let event = eventResizeInfo.event;

                let oldEventEnd = new Date(oldEvent.end.setDate(oldEvent.end.getDate() - 1));
                let eventEnd = new Date(event.end.setDate(event.end.getDate() - 1));

                let newResourceInfo = event.getResources()[0];
                let roomPrice = newResourceInfo.extendedProps.price;
                let roomID = newResourceInfo.extendedProps.room_id;

                $("#room_changes_info, #date_unchange_info").addClass("d-none");
                $("#date_change_info, #price_changes_info, #room_info").removeClass("d-none");

                $("#customer").html(event.title);
                $("#room_description").html(roomID + " (RM " + roomPrice.toFixed(2) +" per night)")
                $("#before_start_date").html(properDateFormat(oldEvent.start));
                $("#before_end_date").html(properDateFormat(oldEventEnd));
                $("#after_start_date").html(properDateFormat(event.start));
                $("#after_end_date").html(properDateFormat(eventEnd));

                let oldTotalPrice = roomPrice * dateDifferenceInDays(oldEvent.start, oldEventEnd);
                let newTotalPrice = roomPrice * dateDifferenceInDays(event.start, eventEnd);
                $("#modified_price").html("RM " + oldTotalPrice.toFixed(2) + " → RM " + newTotalPrice.toFixed(2));

                $("#drag-drop-modal").modal("show");
                $("#undoBtn, #saveBtn").unbind();
                $("#undoBtn").on("click", function() {
                    eventResizeInfo.revert();
                });
                $("#saveBtn").on("click", function() {
                    let eventID = event.id;
                    let startDateISO = dateISOString(event.start);
                    let endDateISO = dateISOString(eventEnd);
                    $.ajax({
                        type: "POST",
                        url: "{{ route("dashboard.reservation-update") }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: eventID,
                            start_date: startDateISO,
                            end_date: endDateISO
                        }
                    });
                });
            }
        });
        calendar.render();
        $("#refetch-event").on("click", function(e) {
            e.preventDefault();
            calendar.refetchResources();
            calendar.refetchEvents();
        });
    });
</script>
@endpush
