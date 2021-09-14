@extends("dashboard.layouts.template")

@push("css")
<style>
    .revenueYearChart {
        position: relative;
        height: 300px;
    }

    .revenueMonthChart {
        position: relative;
        height: 300px;
    }
</style>
@endpush

@section("title")

@endsection

@section("content")
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Revenue (Year)</div>
            <div class="card-body">
                <div class="revenueYearChart">
                    <canvas id="revenueYearChart"></canvas>
                </div>
            </div>

            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Booking</h5>
                        <h6 class="mb-0">RM <span id="bookingRevenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Room Service</h5>
                        <h6 class="mb-0">RM <span id="roomServiceRevenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Additional Charge</h5>
                        <h6 class="mb-0">RM <span id="chargeRevenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Total Revenue</h5>
                        <h6 class="mb-0">RM <span id="totalRevenue"></span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-6">
        <div class="card">
            <div class="card-header">Revenue (Month)</div>
            <div class="card-body">
                <div class="revenueMonthChart">
                    <canvas id="revenueMonthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4 col-xl-4">
        <div class="card">
            <div class="card-header">Weekly sales
                <div class="card-action">
                    <div class="dropdown">
                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                            <i class="icon-options"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void();">Action</a>
                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-2">
                    <canvas id="chart2"></canvas>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center">
                    <tbody>
                        <tr>
                            <td><i class="fa fa-circle text-white mr-2"></i> Direct</td>
                            <td>$5856</td>
                            <td>+55%</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-circle text-light-1 mr-2"></i>Affiliate</td>
                            <td>$2602</td>
                            <td>+25%</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-circle text-light-2 mr-2"></i>E-mail</td>
                            <td>$1802</td>
                            <td>+15%</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-circle text-light-3 mr-2"></i>Other</td>
                            <td>$1105</td>
                            <td>+5%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">Recent Order Tables
                <div class="card-action">
                    <div class="dropdown">
                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                            <i class="icon-options"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void();">Action</a>
                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Photo</th>
                            <th>Product ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Shipping</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Iphone 5</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405822</td>
                            <td>$ 1250.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 90%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Earphone GL</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405820</td>
                            <td>$ 1500.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 60%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>HD Hand Camera</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405830</td>
                            <td>$ 1400.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 70%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Clasic Shoes</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405825</td>
                            <td>$ 1200.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Hand Watch</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405840</td>
                            <td>$ 1800.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 40%"></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Clasic Shoes</td>
                            <td><img src="https://via.placeholder.com/110x110" class="product-img" alt="product img"></td>
                            <td>#9405825</td>
                            <td>$ 1200.00</td>
                            <td>03 Aug 2017</td>
                            <td>
                                <div class="progress shadow" style="height: 3px;">
                                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!--End Row-->
@endsection

@php
    $now = Carbon\Carbon::now();
    $yearNow = $now->year;
    $monthNow = $now->month;
@endphp

@section("right-sidebar")
<div class="right-sidebar">
    <div class="switcher-icon">
        <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
    </div>
    <div class="right-sidebar-content">
        <p class="mb-0">Year</p>
        <hr>
        <div class="form-group">
            <select class="form-control" name="year" id="year">
                <option value="{{ $yearNow }}" selected>{{ $yearNow }}</option>
                @foreach ($years as $year)
                    @if ($year == $yearNow)
                        @continue
                    @endif
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <p class="mb-0 mt-5">Month</p>
        <hr>
        <div class="form-group">
            <select class="form-control w-100" name="month" id="month">
                <option value="01" @if ($monthNow == 1) selected @endif>January</option>
                <option value="02" @if ($monthNow == 2) selected @endif>February</option>
                <option value="03" @if ($monthNow == 3) selected @endif>March</option>
                <option value="04" @if ($monthNow == 4) selected @endif>April</option>
                <option value="05" @if ($monthNow == 5) selected @endif>May</option>
                <option value="06" @if ($monthNow == 6) selected @endif>June</option>
                <option value="07" @if ($monthNow == 7) selected @endif>July</option>
                <option value="08" @if ($monthNow == 8) selected @endif>August</option>
                <option value="09" @if ($monthNow == 9) selected @endif>September</option>
                <option value="10" @if ($monthNow == 10) selected @endif>October</option>
                <option value="11" @if ($monthNow == 11) selected @endif>November</option>
                <option value="12" @if ($monthNow == 12) selected @endif>December</option>
            </select>
        </div>

        <p class="mb-0 mt-5">Room Type</p>
        <hr>
        <div class="form-group">
            <select class="form-control w-100" name="roomType" id="roomType">
                <option value="">All</option>
                @foreach ($roomTypes as $roomType)
                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@endsection

@push("script")
<script src="{{ asset("dashboard/plugins/Chart.js/Chart.min.js") }}"></script>
{{-- <script src="{{ asset("dashboard/js/index.js") }}"></script> --}}
<script>
    $(document).ready(function () {
        $("#year, #month, #roomType").select2();
        $('.select2.select2-container').addClass('form-control');
        const MONTH = {
            1: "January",
            2: "February",
            3: "March",
            4: "April",
            5: "May",
            6: "June",
            7: "July",
            8: "August",
            9: "September",
            10: "October",
            11: "November",
            12: "December"
        }

        var revenueYearChart = null;
        var revenueMonthChart = null;

        function sum(accumulator, value) {
            return accumulator + value;
        }

        function generateRevenueYearChart(info, year, roomType) {
            let bookings = info["bookings"];
            let services = info["services"];
            let charges = info["charges"];
            let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
            let totalBooking = bookings.reduce(sum, 0);
            let totalRoomService = services.reduce(sum, 0);
            let totalCharge = charges.reduce(sum, 0);
            let totalRevenue = revenues.reduce(sum, 0);
            $("#bookingRevenue").html(totalBooking.toFixed(2));
            $("#roomServiceRevenue").html(totalRoomService.toFixed(2));
            $("#chargeRevenue").html(totalCharge.toFixed(2));
            $("#totalRevenue").html(totalRevenue.toFixed(2));

            let revenueYearCanvas = document.getElementById("revenueYearChart").getContext("2d");
            revenueYearChart = new Chart(revenueYearCanvas, {
                type: 'line',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    datasets: [
                        {
                            label: 'Total',
                            data: revenues,
                            backgroundColor: "darkgray",
                            borderColor: "darkgray",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Booking',
                            data: bookings,
                            backgroundColor: "blue",
                            borderColor: "blue",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Room Service',
                            data: services,
                            backgroundColor: "yellow",
                            borderColor: "yellow",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Charges',
                            data: charges,
                            backgroundColor: "red",
                            borderColor: "red",
                            borderWidth: 0,
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: "right",
                        labels: {
                            fontColor: '#ddd',
                            boxWidth: 20
                        }
                    },
                    title: {
                        display: true,
                        text: "Revenue Chart in Year " + year + " (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors: false,
                        callbacks: {
                            label: function (context, constant) {
                                return constant.datasets[context.datasetIndex].label + ": RM " + context.yLabel.toFixed(2);
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero:true,
                                fontColor: '#ddd'
                            },
                            gridLines: {
                                display: true,
                                color: "rgba(221, 221, 221, 0.08)"
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Month',
                                fontColor: "white",
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                fontColor: '#ddd',
                                callback: function(value, index, values) {
                                    return 'RM ' + value;
                                }
                            },
                            gridLines: {
                                display: true,
                                color: "rgba(221, 221, 221, 0.08)"
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Revenue',
                                fontColor: "white",
                            }
                        }]
                    }
                }
            });
        }

        function generateRevenueMonthChart(info, year, month, roomType) {
            let revenueMonthCanvas = document.getElementById("revenueMonthChart").getContext("2d");
            revenueMonthChart = new Chart(revenueMonthCanvas, {
                type: 'pie',
                data: {
                    labels: ["Booking", "Room Service", "Charges"],
                    datasets: [{
                        backgroundColor: [
                            "blue",
                            "yellow",
                            "red"
                        ],
                        data: info
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: "right",
                        labels: {
                            fontColor: '#ddd',
                            boxWidth: 20
                        }
                    },
                    title: {
                        display: true,
                        text: "Revenue of " + MONTH[month] + " " + year + " (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors:false,
                        callbacks: {
                            label: function (context, constant) {
                                let index = context.index;
                                return constant.labels[index] + ": ";
                            },
                            afterLabel: function (context, constant) {
                                let datasetIndex = context.datasetIndex;
                                let index = context.index;
                                let amount = constant.datasets[datasetIndex].data[index];
                                let total = constant.datasets[datasetIndex].data.reduce(sum, 0);
                                return "RM " + amount.toFixed(2) + " (" + (amount / total * 100).toFixed(2) + "%)";
                            }
                        }
                    },
                }
            });
        }

        function updateRevenueYearChart(info, year, roomType) {
            let bookings = info["bookings"];
            let services = info["services"];
            let charges = info["charges"];
            let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
            let totalBooking = bookings.reduce(sum, 0);
            let totalRoomService = services.reduce(sum, 0);
            let totalCharge = charges.reduce(sum, 0);
            let totalRevenue = revenues.reduce(sum, 0);
            $("#bookingRevenue").html(totalBooking.toFixed(2));
            $("#roomServiceRevenue").html(totalRoomService.toFixed(2));
            $("#chargeRevenue").html(totalCharge.toFixed(2));
            $("#totalRevenue").html(totalRevenue.toFixed(2));
            revenueYearChart.data.datasets[0].data = revenues;
            revenueYearChart.data.datasets[1].data = bookings;
            revenueYearChart.data.datasets[2].data = services;
            revenueYearChart.data.datasets[3].data = charges;
            revenueYearChart.options.title.text = "Revenue Chart in Year " + year + " (" + roomType + ")";
            revenueYearChart.update();
        }

        function updateRevenueMonthChart(info, year, month, roomType) {
            revenueMonthChart.data.datasets[0].data = info;
            revenueMonthChart.options.title.text = "Revenue of " + MONTH[month] + " " + year + " (" + roomType + ")";
            revenueMonthChart.update();
        }

        function dateModified(isInitialize) {
            let year = $("#year").val();
            let month = $("#month").val();
            let roomTypeID = $("#roomType").val();
            let roomType = $("#roomType").children("option:selected").html();
            roomType = roomType != "" ? roomType : "All Room Type";
            $.ajax({
                type: "GET",
                url: "{{ route("dashboard.analysis.json") }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "year": year,
                    "month": month,
                    "roomType": roomTypeID
                },
                success: function (response) {
                    year = parseInt(year);
                    month = parseInt(month);
                    if (isInitialize) {
                        generateRevenueYearChart(response["revenueYearChart"], year, roomType);
                        generateRevenueMonthChart(response["revenueMonthChart"], year, month, roomType);
                    }
                    else {
                        updateRevenueYearChart(response["revenueYearChart"], year, roomType);
                        updateRevenueMonthChart(response["revenueMonthChart"], year, month, roomType);
                    }
                }
            });
        }

        $("#year, #month, #roomType").on("change", function() {
            dateModified(false);
        });
        dateModified(true);
    });
</script>

@endpush
