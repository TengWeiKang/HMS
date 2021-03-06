@extends("dashboard.layouts.template")

@push("css")
<style>
.table-input {
    padding: 0 .75rem;
    width: 80px;
    height: 1.75rem;
    display: inherit;
}

.modal input:not([type="submit"]){
    border: 1px solid #aaa;
    color:black !important;
    border-radius: 30px !important;
}
</style>
@endpush

@section("title")
    Dashboard | Make Payment
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
                <form id="payment-form" action="{{ route("dashboard.payment.create", ["reservation" => $reservation]) }}" method="POST">
                    @csrf
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
                                @foreach ($reservation->rooms as $room)
                                    <tr>
                                        <td class="align-middle">{{ $room->room_id }} - {{ $room->name }}<br>({{ $reservation->start_date->format("d M Y") }} - {{ $reservation->end_date->format("d M Y") }})</td>
                                        <td class="align-middle">RM {{ number_format($room->type->price, 2) }}</td>
                                        <td class="align-middle">{{ $reservation->dateDifference() }}</td>
                                        <td class="align-middle">RM {{ number_format($room->type->price * $reservation->dateDifference(), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td width="80%" class="text-right">Booking Price:</td>
                                    <td>RM {{ number_format($reservation->bookingPrice(), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if ($reservation->services->count())
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
                                @forelse ($reservation->services as $service)
                                    <tr>
                                        <td class="align-middle">{{ $service->name }}<br>Purchased on: {{ $service->pivot->created_at->format("d M Y  h:ia") }}</td>
                                        <td class="align-middle">RM {{ number_format($service->price, 2) }}</td>
                                        <td class="align-middle">{{ $service->pivot->quantity }}</td>
                                        <td class="align-middle">RM {{ number_format($service->price * $service->pivot->quantity, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <th colspan="6" class="text-center">No Room Service Found</th>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <div class="table-responsive">
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
                                    <td>- RM {{ number_format($reservation->deposit, 2) }}</td>
                                    <input type="hidden" name="deposit" value="{{ $reservation->deposit }}" readonly>
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
                    <div class="form-group mt-3 text-right mr-3">
                        <button type="button" data-toggle="modal" data-target="#payment-modal" class="btn btn-light btn-round px-5"><i class="icon-check"></i> Payment</button>
                    </div>
                    <div class="modal fade overflow-hidden" id="payment-modal" role="dialog" aria-labelledby="Bank Information" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bank Information</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row mx-2">
                                        <div class="col-12">
                                            <label for="cardNumber">Card Number</label>
                                            <input type="number" id="cardNumber" name="cardNumber" class="form-control" placeholder="Card Number" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mx-2">
                                        <div class="col-6">
                                            <label for="expiredDate">Expired Date</label>
                                            <input type="month" class="form-control" name="expiredDate" placeholder="Card Number" min="{{ date("Y-m") }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="cvv">CVV/CVV2</label>
                                            <input type="password" class="form-control" id="cvv" name="cvv" placeholder="Card Number" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mx-2">
                                        <div class="col-12">
                                            <label for="">Payment Amount: RM <span id="total-payment"></span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                            </div>
                        </div>
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
            function thousandFormat(number) {
                number = number.toFixed(2);
                var parts = number.toString().split(".", 2);
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                if (!parts[1]) {
                    parts[1] = "00";
                }
                return parts.join(".");
            }
            function updatePrices() {
                let price = parseFloat("{{ $reservation->finalPrices() }}");
                let discount = $("#discount").val();
                if (discount == "") {
                    discount = 0;
                }
				else {
					discount = parseFloat(discount);
					if (discount < 0) {
						discount = 0;
						$("#discount").val(0);
					}
					else if (discount > 100) {
						discount = 100;
						$("#discount").val(100);
					}
				}
                let discountedPrice = price * ((100 - discount) / 100);
                $("#price")[0].innerHTML = thousandFormat(discountedPrice);

                $charges = $("input[name='chargePrices[]']");
                let deposit = $("input[name='deposit']").val();
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
                let totalPrice = discountedPrice + chargePrices - deposit;
                $("#finalPrice, #total-payment").html(thousandFormat(totalPrice));
            }

            function bindListener() {
                $(".delete-charge-row, #discount, input[name='chargePrices[]']").unbind();
                $(".delete-charge-row").on("click", function () {
                    $(this).parent().parent().remove();
                    updatePrices();
                });
				$("#discount, input[name='chargePrices[]']").on("input", function () {
					updatePrices();
				});
            }

            $("#cvv").on("input", function() {
                this.setCustomValidity("");
                this.value = this.value.substr(0, 3);
            });

            $("#cardNumber").on("input", function() {
                this.setCustomValidity("");
                this.value = this.value.substr(0, 16);
            });

            $("#payment-form").on("submit", function(e) {
                let cvv = $("#cvv")[0];
                let cardNumber = $("#cardNumber")[0];
                if (cvv.value.length != 3) {
                    cvv.setCustomValidity("CVV must be exact 3 character");
                    cvv.reportValidity();
                    e.preventDefault();
                }
                if (cardNumber.value.length != 16) {
                    cardNumber.setCustomValidity("Card number must be exact 16 number");
                    cardNumber.reportValidity();
                    e.preventDefault();
                }
            });

            $(".add-charge-row").on("click", function () {
                let chargeInput = `<tr>
                    <td><input type="text" name="description[]" class="form-control form-control-rounded table-input" style="width: 90%" required></td>
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
            });
            updatePrices();
            bindListener();
        });

    </script>
@endpush
