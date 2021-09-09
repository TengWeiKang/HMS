@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | Payments
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Payments Record</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reserved By</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Payment At</th>
                                <th>Total Services</th>
                                <th>Total Payment</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $payment->reservable->username }}</td>
                                    <td>{{ $payment->start_date->format("d M Y") }}</td>
                                    <td>{{ $payment->end_date->format("d M Y") }}</td>
                                    <td>{{ $payment->payment_at->format("d M Y h:ia") }}</td>
                                    <td class="text-center">{{ count($payment->charges) }}</td>
                                    <td>RM {{ number_format($payment->totalPrices(), 2) }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.reservation.view", ["reservation" => $payment->reservation]) }}" title="View Reservation">
                                            <i class="fa fa-ticket text-white"></i>
                                        </a>
                                        <a href="{{ route("dashboard.payment.view", ["payment" => $payment]) }}" title="View Payment">
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
        });
    </script>
@endpush
