@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | {{ $facility->name }}
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Edit Facility Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.facility.edit", ["facility" => $facility]) }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label for="facility">Facility</label>
                        <input type="text" class="form-control form-control-rounded @error("facility") border-danger @enderror" name="facility" placeholder="Facility Name" value="{{ old("facility", $facility->name) }}">
                        @error("facility")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="icheck-material-white">
                            <input type="checkbox" id="default" name="default" @if ($facility->default) checked @endif/>
                            <label for="default">Default</label>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-pencil"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
