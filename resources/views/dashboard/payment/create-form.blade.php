@extends("dashboard.layouts.template")

@push("css")
<style>
.table-input {
    padding: 0 .75rem;
    width: 80px;
    height: 1.75rem;
    display: inherit;
}
</style>
@endpush

@section("title")

@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Payment Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <form action="{{ route("dashboard.payment.create", ["reservation" => $reservation]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
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
                                    <td class="align-middle">{{ $reservation->room->room_id }} - {{ $reservation->room->name }}<br>({{ $reservation->start_date->format("d M Y") }} - {{ $reservation->end_date->format("d M Y") }})</td>
                                    <td class="align-middle">RM {{ number_format($reservation->room->price, 2) }}</td>
                                    <td class="align-middle">{{ $reservation->dateDifference() }}</td>
                                    <td class="align-middle">RM {{ number_format($reservation->bookingPrice(), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if ($reservation->services)
                    <div class="card-title mt-5">Room Services</div>
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
                                @foreach ($reservation->services as $service)
                                <tr>
                                    <td class="align-middle">{{ $service->name }}</td>
                                    <td class="align-middle">RM {{ number_format($service->price, 2) }}</td>
                                    <td class="align-middle">{{ $service->pivot->quantity }}</td>
                                    <td class="align-middle">RM {{ number_format($service->price * $service->pivot->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <div class="table-responsive mt-5">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td width="80%" class="text-right">Subtotal:</td>
                                    <td>RM {{ number_format($reservation->finalPrices(), 2) }}</td>
                                </tr>
                                <tr>
                                    <td width="80%" class="text-right">Discount (%):</td>
                                    <td><input type="number" id="discount" name="discount" class="form-control form-control-rounded table-input" min="0" step="1" value="0" required></td>
                                </tr>
                                <tr>
                                    <td width="80%" class="text-right">Total:</td>
                                    <td>RM <span id="price">{{ number_format($reservation->finalPrices(), 2) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-title mt-5">Additional Charges (Optional)</div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="70%">Description</th>
                                    <th width="10%">Action</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td class="text-center">
                                        <a class="add-charge-row" style="cursor: pointer; font-size: 20px">
                                            <i class="zmdi zmdi-plus text-white"></i>
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">Final Total:</td>
                                    <td>RM <span id="finalPrice">{{ number_format($reservation->finalPrices(), 2) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>

        $(document).ready(function () {
            function updatePrices() {
                let price = parseFloat("{{ $reservation->finalPrices() }}");
                let discount = $("#discount").val();
                if (discount == 0) {
                    discount = 0;
                }
                else if (discount < 0) {
                    discount = 0;
                    $("#discount").val(0);
                }
                else if (discount > 100) {
                    discount = 100;
                    $("discount").val(100);
                }
                let discountedPrice = price * ((100 - parseFloat(discount)) / 100);
                $("#price")[0].innerHTML = discountedPrice.toFixed(2);

                $charges = $("input[name='chargePrices[]']");
                let chargePrices = 0;
                $charges.each(function (index, element) {
                    let chargePrice = element.value;
                    if (chargePrice == "") {
                        chargePrice = 0;
                    }
                    if (chargePrice < 0) {
                        chargePrice = 0;
                        element.value = chargePrice;
                    }
                    chargePrices += parseFloat(chargePrice);
                });
                let totalPrice = discountedPrice + chargePrices;
                $("#finalPrice")[0].innerHTML = totalPrice.toFixed(2);
            }

            function bindListener() {
                $(".delete-charge-row").on("click", function () {
                    $(this).parent().parent().remove();
                });
            }

            bindListener();

            $(".add-charge-row").on("click", function () {
                let chargeInput = `<tr>
                    <td><input type="text" name="" class="form-control form-control-rounded table-input" style="width: 90%" required></td>
                    <td class="text-center">
                        <a class="delete-charge-row" style="cursor: pointer; font-size: 20px">
                            <i class="zmdi zmdi-delete text-white"></i>
                        </a>
                    </td>
                    <td>RM <input type="number" name="chargePrices[]" class="form-control form-control-rounded table-input" min="0.01" step="0.01" value="0" required></td>
                </tr>`;
                $(chargeInput).insertBefore($(this).parent().parent());
                bindListener();
                updatePrices();
            })

            $("#discount, input[name='chargePrices[]']").on("input", function () {
                updatePrices();
            });
        });

    </script>
@endpush
