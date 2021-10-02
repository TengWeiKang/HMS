@extends("dashboard.layouts.template")

@push("css")
<style>
    #select2-filterStatus-container {
        color: white;
    }
</style>
@endpush

@section("title")
    Dashboard | Reservations
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Reservations
                @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                <div class="card-action">
                    <a href="{{ route("dashboard.reservation.create") }}"><u><span>Create New Reservation</span></u></a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="filterStatus">Reservation Status</label>
                        <select id="filterStatus">
                            <option value="">All</option>
                            @foreach (App\Models\Reservation::STATUS as $status)
                                <option value="{{ $status["status"] }}">{{ $status["status"] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @php
                        $today = Carbon\Carbon::today();
                        $startDate = new Carbon\Carbon($today->year . "-" . $today->month . "-1");
                        $endDate = new Carbon\Carbon($today->year . "-" . $today->month . "-" . $today->daysInMonth)
                    @endphp
                    <div class="col-3">
                        <label for="startDate">Start Date</label>
                        <input type="date" id="startDate" class="form-control" value="{{ $startDate->format("Y-m-d") }}">
                    </div>
                    <div class="col-3">
                        <label for="endDate">End Date</label>
                        <input type="date" id="endDate" class="form-control" value="{{ $endDate->format("Y-m-d") }}">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room ID</th>
                                <th>Customer</th>
                                <th>Arrival Date</th>
                                <th>Departure Date</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->id() }}</td>
                                    <td>
                                        @foreach ($reservation->rooms as $room)
                                        <a class="hyperlink" href="{{ route("dashboard.room.view", ["room" => $reservation->room]) }}">{{ $reservation->room->room_id }}</a><span style="color: {{ $reservation->room->statusColor() }}"> ({{ $reservation->room->statusName(false) }})</span></td>
                                        @endforeach
                                    <td><a class="hyperlink" href="{{ route("dashboard.customer.view", ["customer" => $reservation->customer]) }}">{{ $reservation->customer->fullName() }}</td>
                                    <td>{{ $reservation->start_date->format("d M Y") }}</td>
                                    <td>{{ $reservation->end_date->format("d M Y") }}</td>
                                    <td style="color: {{ $reservation->statusColor() }}">{{ $reservation->statusName() }}</td>
                                    <td class="text-center action-col">
                                        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                            {{-- TODO validate room status --}}
                                            @if ($reservation->status() == 0 /* && in_array($reservation->rooms->status(), [0, 1]) */ && $reservation->canCheckIn())
                                            <a class="checkInRoom" data-id="{{ $reservation->id }}" data-rooms="{{ $reservation->rooms->pivot }}" style="cursor: pointer" title="Check In">
                                                <i class="fa fa-download text-white"></i>
                                            </a>
                                            @endif
                                            @if (in_array($reservation->status(), [0, 1]))
                                            <a href="{{ route("dashboard.reservation.edit", ["reservation" => $reservation]) }}" title="Edit">
                                                <i class="zmdi zmdi-edit text-white"></i>
                                            </a>
                                            @endif
                                            @if ($reservation->status() == 1)
                                            <a href="{{ route("dashboard.reservation.service", ["reservation" => $reservation]) }}" title="Add Room Service">
                                                <i class="zmdi zmdi-plus text-white"></i>
                                            </a>
                                            @endif
                                            @if ($reservation->status() != 2)
                                            <a class="deleteReservation" data-id="{{ $reservation->id }}" data-number="{{ $reservation->id() }}" style="cursor: pointer" title="Delete">
                                                <i class="zmdi zmdi-delete text-white"></i>
                                            </a>
                                            @endif
                                        @endif
                                        <a href="{{ route("dashboard.reservation.view", ["reservation" => $reservation]) }}" title="View">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
                                        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $reservation->check_in != null && $reservation->check_out == null)
                                        <a href="{{ route("dashboard.payment.create", ["reservation" => $reservation]) }}" title="Check Out">
                                            <i class="zmdi zmdi-check text-white"></i>
                                        </a>
                                        @endif
                                        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $reservation->status() == 0)
                                            <a class="cancelReservation" data-id="{{ $reservation->id }}" data-number="{{ $reservation->id() }}" style="cursor: pointer" title="Cancelled">
                                                <i class="fa fa-times text-white"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            function filterDatatable() {
                table = $("#table").dataTable();
                let value = $("#filterStatus").val();
                table.fnFilter(value, 5, false, true, true, true);
            }
            $("#table").DataTable({
                "columnDefs": [
                {
                    "targets": 6,
                    "width": "7%",
                    "orderable": false,
                    "searchable": false
                }]
            });

            $filterSelect = $('select#filterStatus');
            $filterSelect.select2();
            $('.select2.select2-container').addClass('form-control');
            $filterSelect.on("select2:select", function (e) {
                filterDatatable();
            });

            $("input[type='date']").on("input", function() {
                filterDatatable();
            });

            $.fn.DataTable.ext.search.push(
                function(settings, searchData, index, rowData, counter) {
                    // console.log(settings, searchData, index, rowData, counter);
                    let filterStartDate = new Date($("#startDate").val()) ?? null;
                    let filterEndDate = new Date($("#endDate").val()) ?? null;
                    filterStartDate.setHours(0, 0, 0, 0);
                    filterEndDate.setHours(0, 0, 0, 0);
                    let dataStartDate = new Date(searchData[3]);
                    let dataEndDate = new Date(searchData[4]);

                    if (filterStartDate != null && filterStartDate > dataEndDate) {
                        return false;
                    }
                    if (filterEndDate != null && filterEndDate < dataStartDate)
                        return false
                    return true;
                }
            )

            $(".deleteReservation").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.reservation.destroy', ':id') }}";
                var reservationID = $(this).data("id");
                var reservationNumber = $(this).data("number");
                var url = DELETE_URL.replace(":id", reservationID);
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to remove reservation " + reservationNumber + "?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (response){
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response["success"],
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1000,
                                }).then(() => {
                                    window.location.href = "{{ route("dashboard.reservation") }}";
                                });
                            }
                        });
                    }
                })
            });
            $(".cancelReservation").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.reservation.cancel', ':id') }}";
                var reservationID = $(this).data("id");
                var reservationNumber = $(this).data("number");
                var url = DELETE_URL.replace(":id", reservationID);
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to cancel reservation " + reservationNumber + "?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "PUT",
                            url: url,
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (response){
                                Swal.fire({
                                    title: "Updated!",
                                    text: response["success"],
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1000,
                                }).then(() => {
                                    window.location.href = "{{ route("dashboard.reservation") }}";
                                });
                            }
                        });
                    }
                })
            });
            filterDatatable();
        });
    </script>
@endpush
