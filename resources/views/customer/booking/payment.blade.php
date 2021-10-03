@extends("customer.layouts.template")

@push("css")

@endpush

@section("title")
    Hotel Booking | Payment
@endsection

@section("title2")
    View Payment
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Payment Information <a class="ml-3 font-weight-normal" style="color: blue" href="{{ route("customer.booking.view", ['booking' => $payment->reservation]) }}"><u>{{ $payment->reservation->id() }}</u></a></div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <div class="card-title mt-4">Initial Payment</div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="40%">Description</th>
                                <th width="20%">Unit Price</th>
                                <th width="20%">Night(s)</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="align-middle">{{ $payment->room_name }}<br>({{ $payment->start_date->format("d M Y") }} - {{ $payment->end_date->format("d M Y") }})</td>
                                <td class="align-middle">RM {{ number_format($payment->price_per_night, 2) }}</td>
                                <td class="align-middle">{{ $payment->dateDifference() }}</td>
                                <td class="align-middle">RM {{ number_format($payment->bookingPrice(), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if ($payment->items->count())
                <div class="card-title mt-4">Room Services</div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="40%">Description</th>
                                <th width="20%">Unit Price</th>
                                <th width="20%">Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->items as $service)
                            <tr>
                                <td class="align-middle">{{ $service->service_name }}<br>Purchased on: {{ $service->purchase_at->format("d M Y  h:ia") }}</td>
                                <td class="align-middle">RM {{ number_format($service->unit_price, 2) }}</td>
                                <td class="align-middle">{{ $service->quantity }}</td>
                                <td class="align-middle">RM {{ number_format($service->servicePrice(), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td width="80%" class="text-right">Subtotal:</td>
                                <td>RM {{ number_format($payment->subPrice(), 2) }}</td>
                            </tr>
                            <tr>
                                <td width="80%" class="text-right">Discount:</td>
                                <td>{{ $payment->discount }} %</td>
                            </tr>
                            <tr>
                                <td width="80%" class="text-right">Total:</td>
                                <td>RM <span id="price">{{ number_format($payment->totalSubPrices(), 2) }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-title mt-5">Deposit</div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80%">Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Deposit</td>
                                    <td>- RM {{ number_format($payment->deposit, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @if ($payment->charges->count())
                <div class="card-title mt-4">Additional Charges</div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80%">Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->charges as $charge)
                            <tr>
                                <td>{{ $charge->description }}</td>
                                <td>RM {{ number_format($charge->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td width="80%" class="text-right">Final Total:</td>
                                <td>RM {{ number_format($payment->totalPricesWithDeposit(), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
