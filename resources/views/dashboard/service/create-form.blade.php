@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | New Room Service
@endsection

@section("content")
<div class="row mt-3 justify-content-md-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Service Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.service.create") }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" class="form-control form-control-rounded @error("name") border-danger @enderror" name="name" placeholder="New Room Service Name" value="{{ old("name") }}">
                        @error("name")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price">Service Price (RM)</label>
                        <input type="number" class="form-control form-control-rounded @error("price") border-danger @enderror" name="price" placeholder="New Room Service Price" value="{{ old("price") }}" step="0.01" min="0.01">
                        @error("price")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-plus"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
