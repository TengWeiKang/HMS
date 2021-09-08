@extends("dashboard.layouts.template")

@section("title")
    Dashboard | New Room Type
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Type Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.room-type.edit", ["roomType" => $roomType]) }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label for="name">Room Type Name</label>
                        <input type="text" class="form-control form-control-rounded @error("name") border-danger @enderror" name="name" placeholder="New Room Service Name" value="{{ old("name", $roomType->name) }}">
                        @error("name")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="singleBed">Single Bed Default Value</label>
                        <input type="number" class="form-control form-control-rounded @error("singleBed") border-danger @enderror" name="singleBed" placeholder="Number of Single Bed" value="{{ old("singleBed", $roomType->single_bed) }}" step="1" min="0">
                        @error("singleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="doubleBed">Double Bed Default Value</label>
                        <input type="number" class="form-control form-control-rounded @error("doubleBed") border-danger @enderror" name="doubleBed" placeholder="Number of Double Bed" value="{{ old("doubleBed", $roomType->double_bed) }}" step="1" min="0">
                        @error("doubleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-pencil"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
