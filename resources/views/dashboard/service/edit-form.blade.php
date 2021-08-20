@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | {{ $service->name }}
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Edit Service Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.service.edit", ["service" => $service]) }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" class="form-control form-control-rounded @error("name") border-danger @enderror" name="name" placeholder="Room Service Name" value="{{ old("name", $service->name) }}">
                        @error("name")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price">Service Price</label>
                        <input type="number" class="form-control form-control-rounded @error("price") border-danger @enderror" name="price" placeholder="Service Price" value="{{ old("price", $service->price) }}" step="0.01" min="0.01">
                        @error("price")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
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
