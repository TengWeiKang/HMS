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
                    <table id="table" class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room ID</th>
                                <th>Customer</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
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
                                    <td>{{ $reservation->start_date->format("d M Y") }}</td>
                                    <td>{{ $reservation->end_date->format("d M Y") }}</td>
                                    <td style="color: {{ $reservation->statusColor() }}">{{ $reservation->statusName() }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.reservation.edit", ["reservation" => $reservation]) }}">
                                            <i class="zmdi zmdi-edit text-white"></i>
                                        </a>
                                        <a class="deleteReservation" data-id="{{ $reservation->id }}" data-number="{{ $loop->index + 1 }}" style="cursor: pointer">
                                            <i class="zmdi zmdi-delete text-white"></i>
                                        </a>
                                        <a href="{{ route("dashboard.reservation.view", ["reservation" => $reservation]) }}">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
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
                    "targets": 6,
                    "width": "5%",
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
        });
    </script>
@endpush
