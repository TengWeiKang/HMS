@extends("dashboard.layouts.template")

@push("css")
<style>
    .select2-selection--multiple {
        background-color: inherit !important;
    }
    .select2-selection--multiple--disabled {
        background-color: rgba(21,14,14,.45);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding-left: 5px
    }
</style>
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
                                <option value="{{ $roomType->id }}" data-single="{{ $roomType->single_bed }}" data-double="{{ $roomType->double_bed }}" data-facilities="[{{ implode(", ",$roomType->facilities->pluck("id")->toArray()) }}]" data-image-src="{{ $roomType->imageSrc() }}" @if ($errors->isEmpty() && $roomType->id == $room->type->id ||$errors->isNotEmpty() && old("roomType") == $roomType->id) selected @endif>{{ $roomType->name }} (RM {{ number_format($roomType->price, 2) }})</option>
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
                    <div class="form-group row my-4 mx-2">
                        <div class="col-lg-6 pl-lg-0">
                            <label for="singleBed">Single Bed</label>
                            <input type="number" class="form-control form-control-rounded" id="singleBed" name="singleBed" min="0" step="1" placeholder="Number of Single Bed" value="{{ old("singleBed") }}" disabled>
                        </div>
                        <div class="col-lg-6 pr-lg-0">
                            <label for="doubleBed">Double Bed</label>
                            <input type="number" class="form-control form-control-rounded" id="doubleBed" name="doubleBed" min="0" step="1" placeholder="Number of Double Bed" value="{{ old("doubleBed") }}" disabled>
                        </div>
                    </div>
                    <div class="form-group row mx-2">
                        <label for="facilities">Facilities</label>
                        <select class="form-control form-control-rounded" id="facilities" name="facilities[]" multiple="multiple" disabled>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
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
                    <img id="hotel_preview" class="mw-100" src="{{ $room->type->imageSrc() }}" alt="Hotel PlaceHolder">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function() {
            function updateDefaultBed() {
                let selected = $("#roomType").find(":selected");
                let single = selected.data("single");
                let double = selected.data("double");
                let facilities = selected.data("facilities");
                $("#singleBed").val(single);
                $("#doubleBed").val(double);
                $("#facilities").val(facilities).change();
            };
            $('select.form-control#facilities').select2({
                placeholder: "No Facilities",
            });
            $roomTypeSelect = $('select.form-control#roomType');
            $roomTypeSelect.select2();
            $roomTypeSelect.on("select2:select", function (e) {
                updateDefaultBed();
            });
            $('.select2.select2-container').addClass('form-control form-control-rounded');
            $('.select2-selection--multiple').parents('.select2-container').addClass('form-select-multiple select2-selection--multiple--disabled');
            updateDefaultBed();
        });
    </script>
@endpush
