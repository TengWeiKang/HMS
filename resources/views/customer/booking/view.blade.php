@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | View Booking
@endsection

@section("title")
    View Booking
@endsection



@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#booking" data-toggle="pill" class="nav-link active"><span class="hidden-xs">Booking</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#services" data-toggle="pill" class="nav-link"><span class="hidden-xs">Room Service</span></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="booking">
                    <h5 class="mb-3 font-weight-bold">Booking Info</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="20%">Room:</td>
                                            <td><a href="{{ route("customer.room.view", ["room" => $booking->room]) }}" style="color:blue; text-decoration: underline">{{ $booking->room->room_id . " - " . $booking->room->name}}</a></td>
                                        </tr>
                                        <tr>
                                            <td>Arrival Date:</td>
                                            <td>{{ $booking->start_date->format("d F Y") }}</td>
                                        </tr>
                                        <tr>
                                            <td>Departure Date:</td>
                                            <td>{{ $booking->end_date->format("d F Y") }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Nights:</td>
                                            <td>{{ $booking->dateDifference() . " " . Str::plural("night", $booking->dateDifference() )}}</td>
                                        </tr>
                                        <tr>
                                            <td>Status:</td>
                                            <td style="color: {{ $booking->statusColor() }}">{{ $booking->statusName() }}</td>
                                        </tr>
                                        @if ($booking->check_in != null)
                                            <tr>
                                                <td>Check in:</td>
                                                <td>{{ $booking->check_in->format("d F Y  h:ia") }}</td>
                                            </tr>
                                        @endif
                                        @if ($booking->check_out != null)
                                            <tr>
                                                <td>Check out:</td>
                                                <td>{{ $booking->check_out->format("d F Y  h:ia") }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Booking Price:</td>
                                            <td>RM {{ number_format($booking->bookingPrice(), 2) }}</td>
                                        </tr>
                                        @if ($booking->services->count() > 0)
                                            <tr>
                                                <td>Room Service Price:</td>
                                                <td>RM {{ number_format($booking->totalServicePrices(), 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Total Payment:</td>
                                            <td>RM {{ number_format($booking->finalPrices(), 2) }}
                                                @if ($booking->payment != null)
                                                <a class="pl-3" style="color:blue; text-decoration: underline" href="{{ route("customer.booking.payment", ["payment" => $booking->payment]) }}">View Payment</a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="services">
                    <h5 class="mb-3 font-weight-bold">Room Services</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service Name</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Purchase On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($booking->services as $service)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $service->name }}</td>
                                            <td>RM {{ number_format($service->price, 2) }}</td>
                                            <td>{{ $service->pivot->quantity }}</td>
                                            <td>RM {{ number_format($service->price * $service->pivot->quantity, 2) }}</td>
                                            <td>{{ $service->pivot->created_at->format("d F Y h:ia") }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <th colspan="6" class="text-center">No Room Service Found</th>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if (count($booking->services) > 0)
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right">Total Price:</td>
                                                <td>RM {{ number_format($booking->totalServicePrices(), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- @push('script')
    <script>
        $(document).ready(function () {
            $(".deleteReservation").on("click", function () {
                Swal.fire({
                    title: "Delete Room",
                    text: "Are you sure you want to remove this booking?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('customer.booking.destroy', ["booking" => $booking]) }}",
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
                                    window.location.href = "{{ route("dashboard.reservation.view", ["reservation" => $reservation]) }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush --}}
