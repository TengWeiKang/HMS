@extends("dashboard.layouts.template")

@section("title")
    Dashboard | Customers
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Customers
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                @continue($customer->phone == null)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $customer->fullName() }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.customer.view", ["customer" => $customer]) }}">
                                            <i class="zmdi zmdi-eye text-white"></i>
                                        </a>
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
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            $("#table").DataTable({
                "columnDefs": [
                {
                    "targets": 4,
                    "width": "15%",
                    "orderable": false,
                    "searchable": false
                }]
            });
        });
    </script>
@endpush
