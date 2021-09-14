<section class="banner_area">
    <div class="booking_table d_flex align-items-center">
        <div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
        <div class="container">
            <div class="banner_content text-center">
                <h6>Away from monotonous life</h6>
                <h2>Relax Your Mind</h2>
            </div>
        </div>
    </div>
    <div class="hotel_booking_area position">
        <div class="container">
            <div class="hotel_booking_table">
                <div class="col-md-3">
                    <h2>Search<br> Your Room</h2>
                </div>
                <div class="col-md-9">
                    <div class="boking_table">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="book_tabel_item">
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <input id="arrival" type='text' onfocus="(this.type='date')" class="form-control" min="{{ date("Y-m-d") }}" placeholder="Arrival Date"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class='input-group'>
                                            <input id="departure" type='text' onfocus="(this.type='date')" class="form-control" min="{{ date("Y-m-d") }}" placeholder="Departure Date"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="book_tabel_item">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input id="single" type="number" step="1" min="0" class="wide form-control" placeholder="Single Bed">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input id="double" type="number" step="1" min="0" class="wide form-control" placeholder="Double Bed">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="book_tabel_item">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <select class="wide" id="roomType">
                                                <option data-display="Room Type" value="">All</option>
                                                <optgroup label="Room Type">
                                                    @foreach ($roomTypes as $roomType)
                                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <a class="book_now_btn button_hover" id="search-btn" href="#">Search</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
