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
                        <label for="roomType">Room Type <span class="text-danger">*</span></label>
                        <select class="form-control form-control-rounded" id="roomType" name="roomType">
                            @foreach ($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" data-single="{{ $roomType->single_bed }}" data-double="{{ $roomType->double_bed }}" data-image-src="{{ $roomType->imageSrc() }}" @if ($errors->isEmpty() && $roomType->id == $room->type->id ||$errors->isNotEmpty() && old("roomType") == $roomType->id) selected @endif>{{ $roomType->name }} (RM {{ number_format($roomType->price, 2) }})</option>
                            @endforeach
                        </select>
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
                        <div class="col-12 px-0">
                            <label for="image">Room Image @if ($room->room_image) (Ignore to remain the same image) @endif</label>
                        </div>
                        <div class="col-7 px-0">
                            <input type="file" class="form-control form-control-rounded @error("image") border-danger @enderror" id="image" name="image" min="0.01" step="0.01" placeholder="Room Image" accept=".pdf,.jpg,.png,.jpeg" @if (!$room->room_image) disabled @endif>
                        </div>
                        <div class="col-5">
                            <div class="icheck-material-white">
                                <input type="checkbox" id="default" name="default" @if (!$room->room_image) checked @endif>
                                <label for="default">Use Image from Room Type</label>
                            </div>
                        </div>
                        @error("image")
                        <div class="ml-2 text-sm text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-6 pl-0">
                            <label for="singleBed">Single Bed <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-rounded @error("singleBed") border-danger @enderror" id="singleBed" name="singleBed" min="0" step="1" placeholder="Number of Single Bed" value="{{ old("singleBed", $room->single_bed) }}">
                            @error("singleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 pr-0">
                            <label for="doubleBed">Double Bed <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-rounded @error("doubleBed") border-danger @enderror" id="doubleBed" name="doubleBed" min="0" step="1" placeholder="Number of Double Bed" value="{{ old("doubleBed", $room->double_bed) }}">
                            @error("doubleBed")
                            <div class="ml-2 text-sm text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="facilities">Facilities</label>
                        <select class="form-control form-control-rounded" id="facilities" name="facilities[]" multiple="multiple">
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
                    <img id="hotel_preview" class="mw-100" src="{{ $room->imageSrc() }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function() {
            $('select.form-control#facilities').select2({
                placeholder: "Please select facilities",
                allowClear: true
            });
            $roomTypeSelect = $('select.form-control#roomType');
            $roomTypeSelect.select2();
            $roomTypeSelect.on("select2:select", function (e) {
                let selected = $("#roomType").find(":selected");
                let single = selected.data("single");
                let double = selected.data("double");
                let [file] = $("#image")[0].files;
                $("#singleBed").val(single);
                $("#doubleBed").val(double);
                if (!file) {
                    let src = selected.data("image-src");
                    $("#hotel_preview").attr("src", src);
                }
            });
            $('.select2.select2-container').addClass('form-control form-control-rounded');

            $("#default").on("change", function () {
                let checked = this.checked;
                // use image from room type
                $("#image").prop("disabled", checked);
                if (checked) {
                    $("#image").val("");
                    let src = $("#roomType").find(":selected").data("image-src");
                    $("#hotel_preview").attr("src", src);
                }
            });

            $("#image").on("change", function () {
                const [file] = this.files;
                if (file) {
                    $("#hotel_preview").attr("src", URL.createObjectURL(file));
                }
                else {
                    let src = $("#roomType").find(":selected").data("image-src");
                    $("#hotel_preview").attr("src", src);
                }
            });
        });
    </script>
@endpush
