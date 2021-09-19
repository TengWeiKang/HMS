@extends("dashboard.layouts.template")

@section("title")
    Dashboard | New Room Type
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Room Type Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.room-type.create") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Room Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("name") border-danger @enderror" name="name" placeholder="New Room Service Name" value="{{ old("name") }}">
                        @error("name")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="image">Room Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-rounded @error("image") border-danger @enderror" id="image" name="image" min="0.01" step="0.01" placeholder="Room Image" accept=".pdf,.jpg,.png,.jpeg">
                        @error("image")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price">Room Price per Night (RM) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-rounded @error("price") border-danger @enderror" name="price" min="0.01" step="0.01" placeholder="Room Price" value="{{ old("price") }}">
                        @error("price")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="singleBed">Single Bed Default Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-rounded @error("singleBed") border-danger @enderror" name="singleBed" placeholder="Number of Single Bed" value="{{ old("singleBed") }}" step="1" min="0">
                        @error("singleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="doubleBed">Double Bed Default Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-rounded @error("doubleBed") border-danger @enderror" name="doubleBed" placeholder="Number of Double Bed" value="{{ old("doubleBed") }}" step="1" min="0">
                        @error("doubleBed")
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Image Preview</div>
                <hr>
                <div class="hotel_img text-center">
                    <img id="hotel_preview" class="mw-100" alt="Hotel PlaceHolder" src="{{ asset("dashboard/images/hotel_placeholder.png") }}">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $("#image").on("change", function () {
                const [file] = this.files;
                if (file) {
                    $("#hotel_preview").attr("src", URL.createObjectURL(file));
                }
                else {
                    $("#hotel_preview").attr("src", "{{ asset("dashboard/images/hotel_placeholder.png") }}");
                }
            });
        });
    </script>
@endpush
