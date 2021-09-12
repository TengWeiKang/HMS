@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | Bookings
@endsection

@section("title2")
    Bookings
@endsection

@section("content")
<div class="row mt-3 justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">User Profile</div>
                <hr>
                <div class="row">
                    <div class="table-responsive col-12">
                        <table id="table" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Room ID</th>
                                    <th>Arrival Date</th>
                                    <th>Departure Date</th>
                                    <th>Status</th>
                                    <th>Total Price</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (Auth::user()->bookings as $booking)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $booking->room->room_id }}</td>
                                    <td>{{ $booking->start_date->format("d M Y") }}</td>
                                    <td>{{ $booking->end_date->format("d M Y") }}</td>
                                    <td class="font-weight-lighter" style="color: {{ $booking->statusColor() }}">{{ $booking->statusName() }}</td>
                                    <td>RM {{ number_format($booking->finalPrices(), 2) }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("customer.booking.view", ["booking" => $booking]) }}" title="View">
                                            <i class="lnr lnr-eye"></i>
                                        </a>
                                        @if ($booking->status() == 0)
                                            <a href="{{ route("customer.booking.edit", ["booking" => $booking]) }}" title="Edit">
                                                <i class="lnr lnr-pencil"></i>
                                            </a>
                                            <a class="deleteBooking" data-id="{{ $booking->id }}" data-number="{{ $loop->index + 1 }}" style="cursor: pointer" title="Delete">
                                                <i class="lnr lnr-trash"></i>
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
</div>
@endsection

@push('script')
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
            $(".deleteBooking").on("click", function () {
                const DELETE_URL = "{{ route('customer.booking.destroy', ':id') }}";
                var bookingID = $(this).data("id");
                var bookingNumber = $(this).data("number");
                var url = DELETE_URL.replace(":id", bookingID);
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to cancel booking #" + bookingNumber + "?",
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
                                    window.location.href = "{{ route("customer.booking") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
