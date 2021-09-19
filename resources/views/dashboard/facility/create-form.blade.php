@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | New Facility
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Facility Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.facility.create") }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="facility">Facility Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("facility") border-danger @enderror" name="facility" placeholder="New Facility Name" value="{{ old("facility") }}">
                        @error("facility")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="roomTypes">Rooms Types</label>
                        <select class="form-control form-control-rounded" id="roomTypes" name="roomTypes[]" multiple="multiple">
                            @foreach ($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" @if ($errors->isNotEmpty() && in_array($roomType->id, old("roomTypes"))) selected @endif>{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('select.form-control#roomTypes').select2({
                placeholder: "Please select room types",
                allowClear: true
            });
            $('.select2.select2-container').addClass('form-control form-control-rounded');
            $('.select2-selection--multiple').parents('.select2-container').addClass('form-select-multiple');
        });
    </script>
@endpush
