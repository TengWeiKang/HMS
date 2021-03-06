@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | View Reservation
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#reservation" data-toggle="pill" class="nav-link active"><i class="fa fa-ticket"></i> <span class="hidden-xs">Reservation</span></a>
                    </li>
                    @if (in_array($reservation->status(), [1, 2]))
                        <li class="nav-item">
                            <a href="javascript:void();" data-target="#services" data-toggle="pill" class="nav-link"><i class="zmdi zmdi-drink"></i> <span class="hidden-xs">Room Service</span></a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="reservation">
                    <h5 class="mb-3 font-weight-bold">Reservation Info</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="20%" style="vertical-align: middle">Room:</td>
                                            <td>
                                                @foreach ($reservation->rooms as $room)
                                                    <a href="{{ route("dashboard.room.view", ["room" => $room]) }}" style="color:blue; text-decoration: underline">{{ $room->room_id . " - " . $room->name}}</a> <span style="color: {{ $room->statusColor() }}">({{ $room->statusName(false) }})</span><br>
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Customer:</td>
                                            <td><a class="hyperlink" href="{{ route("dashboard.customer.view", ["customer" => $reservation->customer]) }}">{{ $reservation->customer->fullName() }}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact Number:</td>
                                            <td>{{ $reservation->customer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>Reservation Start Date:</td>
                                            <td>{{ $reservation->start_date->format("d F Y") }}</td>
                                        </tr>
                                        <tr>
                                            <td>Reservation End Date:</td>
                                            <td>{{ $reservation->end_date->format("d F Y") }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Nights:</td>
                                            <td>{{ $reservation->dateDifference() . " " . Str::plural("night", $reservation->dateDifference() )}}</td>
                                        </tr>
                                        <tr>
                                            <td>Status:</td>
                                            <td style="color: {{ $reservation->statusColor() }}">{{ $reservation->statusName() }}</td>
                                        </tr>
                                        @if ($reservation->check_in != null)
                                        <tr>
                                            <td>Check in:</td>
                                            <td>{{ $reservation->check_in->format("d F Y  h:ia") }}</td>
                                        </tr>
                                        @endif
                                        @if ($reservation->check_out != null)
                                        <tr>
                                            <td>Check out:</td>
                                            <td>{{ $reservation->check_out->format("d F Y  h:ia") }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Booking Price:</td>
                                            <td>RM {{ number_format($reservation->bookingPrice(), 2) }}</td>
                                        </tr>
                                        @if ($reservation->services->count() > 0)
                                        <tr>
                                            <td>Room Service Price:</td>
                                            <td>RM {{ number_format($reservation->totalServicePrices(), 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Deposit:</td>
                                            <td>RM {{ number_format($reservation->deposit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Payment:</td>
                                            <td>RM {{ number_format($reservation->finalPrices(), 2) }}
                                                @if ($reservation->payment != null)
                                                <a class="pl-3" style="color:blue; text-decoration: underline" href="{{ route("dashboard.payment.view", ["payment" => $reservation->payment]) }}">View Payment</a>
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
                                <table id="table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service Name</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Purchased on</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reservation->services as $service)
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
                                    @if (count($reservation->services) > 0)
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right">Total Price:</td>
                                            <td colspan="2">RM {{ number_format($reservation->totalServicePrices(), 2) }}</td>
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
    @if ($reservation->status() != 2 && Auth::guard("employee")->user()->isAccessible("frontdesk", "admin"))
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @if ($reservation->canCheckIn())
                        <div class="col-2">
                            <a href="{{ route("dashboard.reservation.check-in", ["reservation" => $reservation]) }}" class="btn btn-primary w-100">
                                Check in
                            </a>
                        </div>
                    @endif
                    @if (in_array($reservation->status(), [0, 1]))
                        <div class="col-2">
                            <a href="{{ route("dashboard.reservation.edit", ["reservation" => $reservation]) }}" class="btn btn-primary w-100">
                                Edit
                            </a>
                        </div>
                    @endif
                    @if ($reservation->status() == 1)
                        <div class="col-2">
                            <a href="{{ route("dashboard.reservation.service", ["reservation" => $reservation]) }}" class="btn btn-primary w-100">
                                Add Service
                            </a>
                        </div>
                        <div class="col-2">
                            <a href="{{ route("dashboard.payment.create", ["reservation" => $reservation]) }}" class="btn btn-primary w-100">
                                Check Out
                            </a>
                        </div>
                    @endif
                    @if ($reservation->status() == 0)
                        <div class="col-2">
                            <a class="cancelReservation btn btn-primary w-100" style="cursor: pointer">
                                Cancel
                            </a>
                        </div>
                    @endif
                    @if ($reservation->status() != 2 && Auth::guard("employee")->user()->isAccessible("admin"))
                        <div class="col-2">
                            <a class="deleteReservation btn btn-primary w-100" style="cursor: pointer">
                                Delete
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $(".deleteReservation").on("click", function () {
                Swal.fire({
                    title: "Delete Reservation",
                    text: "Are you sure you want to remove this reservation?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('dashboard.reservation.destroy', ["reservation" => $reservation]) }}",
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
            $(".cancelReservation").on("click", function () {
                const DELETE_URL = "{{ route('dashboard.reservation.cancel', ':id') }}";
                Swal.fire({
                    title: "Cancel Reservation",
                    text: "Are you sure you want to cancel this reservation?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "PUT",
                            url: "{{ route('dashboard.reservation.cancel', ["reservation" => $reservation]) }}",
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
