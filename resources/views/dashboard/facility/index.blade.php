@extends("dashboard.layouts.template")

@section("title")
    Dashboard | Facilties
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">All Facilities
                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                    <div class="card-action">
                        <a href="{{ route("dashboard.facility.create") }}"><u><span>Create New Facility</span></u></a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Facility</th>
                                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facilities as $facility)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $facility->name }}</td>
                                    @if (Auth::guard("employee")->user()->isAccessible("admin"))
                                    <td class="text-center action-col">
                                        <a href="{{ route("dashboard.facility.edit", ["facility" => $facility]) }}">
                                            <i class="zmdi zmdi-edit text-white"></i>
                                        </a>
                                        <a class="deleteFacility" data-id="{{ $facility->id }}" data-facility="{{ $facility->name }}" style="cursor: pointer">
                                            <i class="zmdi zmdi-delete text-white"></i>
                                        </a>
                                    </td>
                                    @endif
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
                "columnDefs": [{
                    "targets": 0,
                    "width": "7%",
                },
                @if (Auth::guard("employee")->user()->isAccessible("admin"))
                {
                    "targets": 2,
                    "width": "15%",
                    "orderable": false,
                    "searchable": false
                }
                @endif
                ]
            });
            $(".deleteFacility").on("click", function () {
                var facilityId = $(this).data("id");
                var facilityName = $(this).data("facility");
                const DELETE_URL = "{{ route('dashboard.facility.destroy', ':id') }}";
                var url = DELETE_URL.replace(":id", facilityId);
                Swal.fire({
                    title: "Delete Facility",
                    text: "Are you sure you want to remove " + facilityName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: "#E00",
                    confirmButtonColor: "#00E",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: url,
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
                                    window.location.href = "{{ route("dashboard.facility") }}";
                                });
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
