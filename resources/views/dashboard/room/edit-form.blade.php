@extends("dashboard.layouts.template")

@push("css")

@endpush

@section("title")
    Dashboard | {{ $room->room_id . " " . $room->name }}
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Edit Room Form</div>
                @if (session('message'))
                    <div class="text-success text-center">{{ session('message') }}</div>
				@endif
                <hr>
                <form action="{{ route("dashboard.room.edit", ["room" => $room]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")
                    <div class="form-group row mx-2">
                        <label for="roomId">Room ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("roomId") border-danger @enderror" name="roomId" placeholder="Room ID" value="{{ old("roomId", $room->room_id) }}">
                        @error("roomId")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row mx-2">
                        <label for="name">Room Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-rounded @error("name") border-danger @enderror" name="name" placeholder="Room Name" value="{{ old("name", $room->name) }}">
                        @error("name")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row mx-2">
                        <label for="price">Room Price per Night (RM) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-rounded @error("price") border-danger @enderror" name="price" min="0.01" step="0.01" placeholder="Room Price" value="{{ old("price", $room->price) }}">
                        @error("price")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row mx-2">
                        <label for="image">Room Image (Ignore to remain the same image) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-rounded @error("image") border-danger @enderror" id="image" name="image" min="0.01" step="0.01" placeholder="Room Image" accept=".pdf,.jpg,.png,.jpeg">
                        @error("image")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <label class="col-lg-12 px-0">Bed <span class="text-danger">*</span></label>
                        <div class="col-lg-6 pl-0">
                            <input type="number" class="form-control form-control-rounded @error("singleBed") border-danger @enderror" name="singleBed" min="0" step="1" placeholder="Number of Single Bed" value="{{ old("singleBed", $room->single_bed) }}">
                            @error("singleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 pr-0">
                            <input type="number" class="form-control form-control-rounded @error("doubleBed") border-danger @enderror" name="doubleBed" min="0" step="1" placeholder="Number of Double Bed" value="{{ old("doubleBed", $room->double_bed) }}">
                            @error("doubleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="facilities[]">Facilities</label>
                        <select class="form-control form-control-rounded" name="facilities[]" multiple="multiple">
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}" @if ($errors->isNotEmpty() && in_array($facility->id, old("facilities")) || $errors->isEmpty() && in_array($facility->id, $room->facilities->pluck("id")->all())) selected @endif>{{ $facility->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row mt-5 mx-2">
                        <button type="submit" class="btn btn-light btn-round px-5"><i class="icon-pencil"></i> Update</button>
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
                    <img id="hotel_preview" class="mw-100" src="data:{{ $room->image_type }};base64,{{ base64_encode($room->room_image) }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function() {
            $('select.form-control').select2({
                placeholder: "Please select facilities",
                allowClear: true
            });
            $('.select2.select2-container').addClass('form-control form-control-rounded');

            $("#image").on("change", function () {
                const [file] = this.files;
                if (file) {
                    $("#hotel_preview").attr("src", URL.createObjectURL(file));
                }
                else {
                    $("#hotel_preview").attr("src", "data:{{ $room->image_type }};base64,{{ base64_encode($room->room_image) }}");
                }
            });
        });
    </script>
@endpush
