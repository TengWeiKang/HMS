@extends("dashboard.layouts.template")

@push("css")
<style>
.card .table td, .card .table th {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}
.qty-input {
    padding: 0 .75rem;
    width: 80px;
    height: 1.75rem;
    display: inherit;
}
#table-service td {
    text-align: center;
}
.delete-row-service {
    cursor: pointer;
    font-size: 15px;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color:#fff;
}
</style>
@endpush

@section("title")
    Dashboard | Add Services
@endsection

@section("content")
<div class="row mt-3 justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
                    <hr>
				@endif
                <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#add-services" data-toggle="pill" class="nav-link active"><i class="icon-plus"></i> <span class="hidden-xs">Add Services</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void();" data-target="#existing-services" data-toggle="pill" class="nav-link"><i class="fa fa-history"></i> <span class="hidden-xs">Existing Services</span></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="add-services">
                    <h5 class="mb-5 ml-2 font-weight-bold">Room Service for a Reservation</h5>
                    <div class="form-group row mx-2">
                        <label for="service">Search for services</label>
                        <select id="service" class="form-control form-control-rounded row-mx-2">
                            <option value=""></option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" data-name="{{ $service->name }}" data-price="{{ $service->price }}">{{ $service->name . " - RM " . $service->price }}</option>
                            @endforeach
                        </select>
                    </div>
                    <form id="service-form" action="{{ route("dashboard.reservation.service", ["reservation" => $reservation]) }}" method="POST">
                        @csrf
                        <div class="form-group row mx-2">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Service Name</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-service">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-right" colspan="3">Total:</td>
                                            <td class="text-center">RM <span id="totalPrice">0.00</span></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row mt-3 mx-2 float-right">
                            <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Add</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="existing-services">
                    <h5 class="mb-5 ml-2 font-weight-bold">Existing Services</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Purchased On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservation->services as $service)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $service->name }}</td>
                                    <td>RM {{ number_format($service->price, 2) }}</td>
                                    <td>{{ $service->pivot->quantity }}</td>
                                    <td>RM {{ number_format($service->price * $service->pivot->quantity, 2) }}</td>
                                    <td>{{ $service->pivot->created_at->format("d M Y H:i:s") }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"></td>
                                    <td>RM {{ number_format($reservation->totalServicePrices(), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $dropdown = $("#service");
            $dropdown.select2({
                placeholder: {
                    id: '',
                    text: 'Choose Service to Add'
                },
                allowClear: true
            });
            $dropdown.on("select2:select", function (event) {
                $element = $(event.params.data.element);
                $element[0].disabled = true;
                let serviceID = $element.val();
                let serviceName = $element.data("name");
                let price = parseFloat($element.data("price"));
                let html =
                    `<tr class="service-row">
                        <td class="text-left">` + serviceName + `</td>
                        <td>RM ` + price.toFixed(2) + `</td>
                        <td>
                            <input type="hidden" name="serviceID[]" value="` + serviceID + `">
                            <input type="number" name="qty[]" class="form-control form-control-rounded qty-input" min="1" step="1" value="1" data-price="` + price + `" required>
                        </td>
                        <td>RM <span>` + price.toFixed(2) + `</span></td>
                        <td>
                            <a class="delete-row-service" data-id="` + serviceID + `" style="cursor: pointer">
                                <i class="zmdi zmdi-delete text-white"></i>
                            </a>
                        </td>
                    </tr>`;
                $("#table-service").append(html);
                bindListener();
                $dropdown.val(0).trigger('change.select2');
            });
            $('.select2.select2-container').addClass('form-control form-control-rounded');

            $("form#service-form").submit(function (event) {
                event.preventDefault();
                let length = $("form#service-form input").length;
                if (length > 1) { // ignore token input
                    Swal.fire({
                        title: "Confirmation",
                        text: "Are you sure you want to add these services?\nThis process cannot be undo after submit",
                        icon: "warning",
                        showCancelButton: true,
                        cancelButtonColor: "#E00",
                        confirmButtonColor: "#00E",
                        confirmButtonText: "Yes"
                    }).then((result) => {
                        if (result.value) {
                            $(this).unbind("submit").submit();
                        }
                    });
                }
                else {
                    Swal.fire({
                        title: "No Service Added",
                        text: "Please add at least one service before save",
                        icon: "error",
                    });
                }
            });

            function bindListener() {
                $deleteBtn = $(".delete-row-service");
                $deleteBtn.unbind();
                $deleteBtn.on("click", function () {
                    let id = $(this).data("id");
                    $(this).parents(".service-row").remove();
                    $("option[value='" + id + "']")[0].disabled = false;
                    updatePrices();
                });

                $inputs = $("input[name='qty[]']");
                $inputs.unbind();
                $inputs.on("input", function () {
                    let qty = $(this).val();
                    if(qty != "" && qty < 1)
                        $(this).val(1);
                    updatePrices();
                });
                updatePrices();
            }

            function updatePrices() {
                $inputs = $("input[name='qty[]']");
                let totalPrice = 0;
                $inputs.each((index, input) => {
                    let unitPrice = parseFloat(input.getAttribute("data-price"));
                    let quantity = input.value;
                    if (quantity == "")
                        quantity = 0;
                    quantity = parseInt(quantity);
                    let price = unitPrice * quantity;
                    totalPrice += price;
                    input.parentElement.nextElementSibling.getElementsByTagName("span")[0].innerHTML = price.toFixed(2);
                });
                $("#totalPrice")[0].innerHTML = totalPrice.toFixed(2);
            }
        });
    </script>
@endpush
