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
                <li class="nav-item">
                    <a href="javascript:void();" data-target="#services" data-toggle="pill" class="nav-link"><i class="zmdi zmdi-drink"></i> <span class="hidden-xs">Room Service</span></a>
                </li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="reservation">
                    <h5 class="mb-3 font-weight-bold">Reservation Info</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="20%">Room:</td>
                                            <td><a href="{{ route("dashboard.room.view", ["room" => $reservation->room]) }}" style="color:blue; text-decoration: underline">{{ $reservation->room->room_id . " - " . $reservation->room->name}}</a></td>
                                        </tr>
                                        <tr>
                                            <td>Customer:</td>
                                            <td>{{ $reservation->reservable->username }}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact Number:</td>
                                            <td>{{ $reservation->reservable->phone }}</td>
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
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service Name</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
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
                                        </tr>
                                        @empty
                                        <tr>
                                            <th colspan="5" class="text-center">No Room Service Found</th>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    @if (count($reservation->services) > 0)
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right">Total Price:</td>
                                            <td>RM {{ number_format($reservation->totalServicePrices(), 2) }}</td>
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
