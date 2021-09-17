@extends("dashboard.layouts.template")

@push("css")
<style>
    select option {
        background-color: transparent;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black;
    }
    .select2.select2-container {
        border: 1px solid #aaa;
    }
</style>
@endpush

@section("title")
    Dashboard | {{ $customer->username }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#customer" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Customer Info</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#reservation" data-toggle="pill" class="nav-link"><i class="fa fa-history"></i> <span class="hidden-xs">History</span></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="customer">
                    <h5 class="mb-3 font-weight-bold">Customer Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tr>
                                        <td width="20%">Customer Name:</td>
                                        <td>{{ $customer->username }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer Email:</td>
                                        <td>{{ $customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer Contact:</td>
                                        <td>{{ $customer->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer Register At:</td>
                                        <td>{{ $customer->created_at->format("d F Y") }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Number of Reservations:</td>
                                        <td>{{ $customer->bookings->count() }}</td>
                                    </tr>
                                    @php
                                        $totalRevenue = $customer->bookings->sum(function ($booking) {
                                            return optional($booking->payment)->totalPrices() ?? 0;
                                        });
                                    @endphp
                                    <tr>
                                        <td>Total Revenue Collected:</td>
                                        <td>RM {{ number_format($totalRevenue, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="reservation">
                    <h5 class="mb-3 font-weight-bold">Reservation</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Room ID</th>
                                            <th>Arrival Date</th>
                                            <th>Departure Date</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customer->bookings as $booking)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td><a class="hyperlink" href="{{ route("dashboard.room.view", ["room" => $booking->room]) }}">{{ $booking->room->room_id }}</a></td>
                                                <td>{{ $booking->start_date->format("d F Y") }}</td>
                                                <td>{{ $booking->end_date->format("d F Y") }}</td>
                                                <td style="color: {{ $booking->statusColor() }}">{{ $booking->statusName() }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route("dashboard.reservation.view", ["reservation" => $booking]) }}">
                                                        <i class="zmdi zmdi-eye text-white" style="font-size: 18px"></i>
                                                    </a>
                                                    @if ($booking->payment != null)
                                                    <a href="{{ route("dashboard.payment.view", ["payment" => $booking->payment]) }}">
                                                        <i class="fa fa-dollar text-white" style="font-size: 18px"></i>
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
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $("#table").DataTable({
                columnDefs: [
                    {
                        targets: 5,
                        orderable: false,
                        searchable: false
                    }
                ]
            })
        });
    </script>
@endpush
