@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | Reservations
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Reservations
                <div class="card-action">
                    <a href="{{ route("dashboard.reservation.create") }}"><u><span>Create New Reservation</span></u></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room ID</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Arrival Date</th>
                                <th>Departure Date</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $reservation->room->room_id }}</td>
                                    <td>{{ $reservation->reservable->username }}</td>
                                    <td>{{ $reservation->reservable->phone }}</td>
                                    <td>{{ $reservation->start_date->format("d M Y") }}</td>
                                    <td>{{ $reservation->end_date->format("d M Y") }}</td>
                                    <td style="color: {{ $reservation->statusColor() }}">{{ $reservation->statusName() }}</td>
                                    <td class="text-center action-col">
                                        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
                                            @if ($reservation->status() == 0)
                                            <a href="{{ route("dashboard.reservation.check-in", ["reservation" => $reservation]) }}" title="Check In">
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
                                            <a class="deleteReservation" data-id="{{ $reservation->id }}" data-number="{{ $loop->index + 1 }}" style="cursor: pointer" title="Delete">
                                                <i class="zmdi zmdi-delete text-white"></i>
                                            </a>
                                            @endif
                                        @endif
                                        <a href="{{ route("dashboard.reservation.view", ["reservation" => $reservation]) }}" title="View">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
                                        @if (Auth::guard("employee")->user()->isAccessible("frontdesk", "admin") && $reservation->status() == 0)
                                            <a class="cancelReservation" data-id="{{ $reservation->id }}" data-number="{{ $loop->index + 1 }}" style="cursor: pointer" title="Cancelled">
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
            $("#table").DataTable({
                "columnDefs": [
                {
                    "targets": 7,
                    "width": "7%",
                    "orderable": false,
                    "searchable": false
                }]
            });
            $(".deleteReservation").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.reservation.destroy', ':id') }}";
                var reservationID = $(this).data("id");
                var reservationNumber = $(this).data("number");
                var url = DELETE_URL.replace(":id", reservationID);
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to remove reservation #" + reservationNumber + "?",
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
                    text: "Are you sure you want to cancel reservation #" + reservationNumber + "?",
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
        });
    </script>
@endpush
