@extends('dashboard.layouts.template')

@push('css')
<link href="{{ asset("dashboard/plugins/fullcalendar-v5/main.min.css") }}" rel="stylesheet"/>
<style>
    .modal table td {
        color: black;
        padding-top: .25rem;
        padding-bottom: .25rem;
        border-color: gray;
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
    .table-bordered {
        border-color: black;
    }

    .resource-url {
        text-decoration: underline !important;
    }
    .status-available {
        color: {{ App\Models\Room::STATUS[0]["color"] }};
    }
    .status-dirty {
        color: {{ App\Models\Room::STATUS[2]["color"] }};
    }
    .status-repair {
        color: {{ App\Models\Room::STATUS[3]["color"] }};
    }
    .status-reserved {
        color: {{ App\Models\Room::STATUS[4]["color"] }};
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
                <div class="row c-mx">
                    <div class="icheck-material-secondary">
                        <input type="checkbox" id="createNewTab" name="createNewTab"/>
                        <label for="createNewTab">Open in New Tab</label>
                    </div>
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
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Date Modified</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td class="text-center">Before</td>
                                    <td class="text-center">After</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Customer:</td>
                                    <td colspan="2" class="text-center" id="customer"></td>
                                </tr>
                                <tr id="room_info">
                                    <td>Room:</td>
                                    <td colspan="2" class="text-center" id="room_description"></td>
                                </tr>
                                <tr id="room_changes_info">
                                    <td>Room:</td>
                                    <td class="text-center" id="before_room"></td>
                                    <td class="text-center" id="after_room"></td>
                                </tr>
                                <tr id="date_unchange_info">
                                    <td>Date:</td>
                                    <td colspan="2" class="text-center" id="unchanged_date"></td>
                                </tr>
                                <tr id="start_date_change_info">
                                    <td>Start Date:</td>
                                    <td class="text-center" id="before_start_date"></td>
                                    <td class="text-center" id="after_start_date"></td>
                                </tr>
                                <tr id="end_date_change_info">
                                    <td>End Date:</td>
                                    <td class="text-center" id="before_end_date"></td>
                                    <td class="text-center" id="after_end_date"></td>
                                </tr>
                                <tr id="unchanged_night">
                                    <td>Night:</td>
                                    <td colspan="2" class="text-center" id="nights"></td>
                                </tr>
                                <tr id="changed_night">
                                    <td>Night:</td>
                                    <td class="text-center" id="before_night"></td>
                                    <td class="text-center" id="after_night"></td>
                                </tr>
                                <tr id="price_changes_info">
                                    <td>Booking Prices:</td>
                                    <td class="text-center" id="before_modified_price"></td>
                                    <td class="text-center" id="after_modified_price"></td>
                                </tr>
                                <tr id="price_unchanged_info">
                                    <td>Booking Prices:</td>
                                    <td colspan="2" class="text-center" id="unmodified_price"></td>
                                </tr>
                            </tbody>
                        </table>
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
                        <table class="table table-bordered">
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
                @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset("dashboard/plugins/fullcalendar-v5/main.js") }}"></script>
<script>
    $(document).ready(function () {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $("button#redirectBtn").on("click", function() {
            let redirectURL = $("input#redirectURL").val();
            let newTab = $("#createNewTab").prop("checked");
            window.open(redirectURL, (newTab ? "_blank": "_self"));
        });

        function refetchCalendar() {
            calendar.refetchEvents();
            calendar.refetchResources();
        }

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
            resourceOrder: 'title',
            refetchResourcesOnNavigate: true,
            slotLabelFormat: [
                {day: 'numeric', month: 'numeric'},
            ],
            headerToolbar: {
                left: 'prev,today,next',
                center: 'title',
                right: 'resourceTimelineTwoWeek,resourceTimelineMonths,resourceTimelineYears'
            },
            views: {
                resourceTimelineTwoWeek: {
                    type: 'resourceTimeline',
                    duration: { weeks: 2 },
                    slotDuration: { days: 1 },
                    buttonText: '2 weeks',
                    dateIncrement: { weeks: 1 }
                },
                resourceTimelineMonths: {
                    type: 'resourceTimeline',
                    duration: { months: 2 },
                    slotDuration: { days: 1 },
                    buttonText: 'month',
                    dateIncrement: { months: 1 }
                },
                resourceTimelineYears: {
                    type: 'resourceTimeline',
                    duration: { months: 12 },
                    slotDuration: { days: 1 },
                    buttonText: 'year',
                    dateIncrement: { months: 6 }
                },
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
                $("#display-total-night").html(dateDiff + " nights");
                $("#display-total-price").html("RM " + event.extendedProps.totalPrice.toFixed(2));
                $("#button[name='check-out']")
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
                        let roomStatus = event.getResources()[0].extendedProps.status;
                        if (roomStatus == 4) {
                            $("button[name='check-in']").css({"opacity": 0.7, "cursor": "no-drop"});
                        }
                        else {
                            $("button[name='check-in']").removeAttr("style");
                            $("button[name='check-in']").on("click", function() {
                                let newTab = $("#newTab").prop("checked");
                                window.open(CHECK_IN_URL.replace(":reservationID", eventID), (newTab ? "_blank": "_self"));
                                if (newTab) {
                                    refetchCalendar();
                                }
                            });
                        }
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
                            if (newTab) {
                                refetchCalendar();
                            }
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
                    if (newResourceInfo.extendedProps.status == 4 && event.extendedProps.status == 1) {
                        eventDropInfo.revert();
                        Swal.fire({
                            title: "Error",
                            text: "The room is reserved by other customer.",
                            icon: "error",
                        });
                        return;
                    }
                    $("#room_changes_info, #price_changes_info").removeClass("d-none");
                    $("#room_info, #price_unchanged_info").addClass("d-none");
                    $("#before_room").html(oldRoomID + " (RM " + oldRoomPrice.toFixed(2) +" per night)");
                    $("#after_room").html(newRoomID + " (RM " + newRoomPrice.toFixed(2) +" per night)");
                    let oldTotalPrice = oldRoomPrice * dateDifferenceInDays(oldEvent.start, oldEventEnd);
                    let newTotalPrice = newRoomPrice * dateDifferenceInDays(event.start, eventEnd);
                    $("#before_modified_price").html("RM " + oldTotalPrice.toFixed(2));
                    $("#after_modified_price").html("RM " + newTotalPrice.toFixed(2));
                }
                else {
                    $("#room_info, #price_unchanged_info").removeClass("d-none");
                    $("#room_changes_info, #price_changes_info").addClass("d-none");
                    $("#room_description").html(newRoomID + " (RM " + newRoomPrice.toFixed(2) +" per night)")
                    $("#unmodified_price").html("RM " + (newRoomPrice * dateDifferenceInDays(event.start, eventEnd)).toFixed(2))
                }
                let delta = eventDropInfo.delta;

                $("#unchanged_night").removeClass("d-none");
                $("#changed_night").addClass("d-none");
                $("#nights").html(dateDifferenceInDays(event.start, eventEnd) + " nights");
                $("#customer").html(event.title);

                if (delta.days == 0 && delta.months == 0 && delta.years == 0) {
                    $("#date_unchange_info").removeClass("d-none");
                    $("#start_date_change_info, #end_date_change_info").addClass("d-none");
                    $("#unchanged_date").html(properDateFormat(oldEvent.start) + " - " + properDateFormat(oldEventEnd));
                }
                else {
                    $("#date_unchange_info").addClass("d-none");
                    $("#start_date_change_info, #end_date_change_info").removeClass("d-none");
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
                        },
                    });
                    refetchCalendar();
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

                $("#room_changes_info, #date_unchange_info, #unchanged_night, #price_unchanged_info").addClass("d-none");
                $("#start_date_change_info, #end_date_change_info, #price_changes_info, #room_info, #changed_night").removeClass("d-none");

                $("#customer").html(event.title);
                $("#room_description").html(roomID + " (RM " + roomPrice.toFixed(2) +" per night)")
                $("#before_start_date").html(properDateFormat(oldEvent.start));
                $("#before_end_date").html(properDateFormat(oldEventEnd));
                $("#after_start_date").html(properDateFormat(event.start));
                $("#after_end_date").html(properDateFormat(eventEnd));
                $("#before_night").html(dateDifferenceInDays(oldEvent.start, oldEventEnd));
                $("#after_night").html(dateDifferenceInDays(event.start, eventEnd));

                let oldTotalPrice = roomPrice * dateDifferenceInDays(oldEvent.start, oldEventEnd);
                let newTotalPrice = roomPrice * dateDifferenceInDays(event.start, eventEnd);
                $("#before_modified_price").html("RM " + oldTotalPrice.toFixed(2));
                $("#after_modified_price").html("RM " + newTotalPrice.toFixed(2));

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
                    refetchCalendar();
                });
            },
            resourceLabelContent: function(arg, createElement) {
                const REDIRECT_ROOM_TYPE_URL = "{{ route("dashboard.room-type.view", ":id") }}";
                const REDIRECT_ROOM_URL = "{{ route("dashboard.room.view", ":id") }}";
                if (arg.resource.id > 0) {
                    redirectURL = REDIRECT_ROOM_URL.replace(":id", arg.resource.id);
                    let status = arg.resource.extendedProps.status;
                    let status_css = "";
                    switch (status) {
                        case 0:
                            status_css = "status-available";
                            break;
                        case 2:
                            status_css = "status-dirty";
                            break;
                        case 3:
                            status_css = "status-repair";
                            break;
                        case 4:
                            status_css = "status-reserved";
                            break;
                    }
                    return createElement("a", {href: redirectURL, class: "resource-url " + status_css}, arg["fieldValue"]);
                }
                else {
                    redirectURL = REDIRECT_ROOM_TYPE_URL.replace(":id", arg.resource.id * -1);
                    return createElement("a", {href: redirectURL, class: "resource-url"}, arg["fieldValue"]);
                }
            },
            selectAllow: function(selectInfo) {
                if (selectInfo.resource.id < 0)
                    return false;
                return true;
            },
            eventAllow: function(dropInfo, draggedEvent) {
                if (dropInfo.resource.id < 0)
                    return false;
                return true;
            },
        });
        calendar.render();
        $("#refetch-event").on("click", function(e) {
            e.preventDefault();
            refetchCalendar();
        });
    });
</script>
@endpush
