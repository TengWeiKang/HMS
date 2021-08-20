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
</style>
@endpush

@section("title")
    Dashboard | Add Services
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Service for a Reservation</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.reservation.service", ["reservation" => $reservation]) }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group row mx-2">
                        <label for="service">Search for services</label>
                        <select id="service" class="form-control form-control-rounded row-mx-2">
                            <option value=""></option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" data-name="{{ $service->name }}" data-price="{{ $service->price }}">{{ $service->name . " - RM " . $service->price }}</option>
                            @endforeach
                        </select>
                    </div>
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
                                        <td colspan="4"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="form-group row mt-5 mx-2">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-pencil"></i> Update</button>
                    </div> --}}
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Existing Services</div>
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
                            <tr>
                                <th>1</th>
                                <td>Service Name</td>
                                <td>RM 5</td>
                                <td>2</td>
                                <td>RM 10</td>
                            </tr>
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
        $dropdown = $("#service");
        $dropdown.select2({
            placeholder: {
                id: '',
                text: 'Choose Service to Add'
            },
            allowClear: true
        });
        $dropdown.on("select2:select", function (event) {
            console.log(event);
            $element = $(event.params.data.element);
            $element[0].disabled = true;
            let serviceID = $element.val();
            let serviceName = $element.data("name");
            let price = parseFloat($element.data("price"));
            let html =
                `<tr class="service-row">
                    <td>` + serviceName + `</td>
                    <td>RM ` + price.toFixed(2) + `</td>
                    <td>
                        <input type="hidden" name="serviceID[]" value="` + serviceID + `">
                        <input type="number" name="qty[]" class="form-control form-control-rounded qty-input" min="1" step="1" value="0" required>
                    </td>
                    <td>RM <span name='unitTotal'>0.00</span></td>
                    <td>
                        <a class="delete-row-service" data-id="` + serviceID + `" style="cursor: pointer">
                            <i class="zmdi zmdi-delete text-white"></i>
                        </a>
                    </td>
                </tr>`;
            $("#table-service").append(html);
            bindDeleteListener();
            $dropdown.val(0).trigger('change.select2');
        });
        $('.select2.select2-container').addClass('form-control form-control-rounded');

        function bindDeleteListener() {
            $deleteBtn = $(".delete-row-service");
            $deleteBtn.unbind();
            $deleteBtn.on("click", function () {
                console.log(this);
                let id = $(this).data("id");
                $(this).parents(".service-row").remove();
                $("option[value='" + id + "']")[0].disabled = false;
            });
        }
    </script>
@endpush
