@extends("dashboard.layouts.template")

@push("css")
<style>
    .revenueYearChart,
    .roomStatusChart,
    .roomServiceChart {
        position: relative;
        height: 350px;
    }
</style>
@endpush

@section("title")
    Dashboard | Statistics
@endsection

@section("content")
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="mr-2">Revenue</span><span class="badge badge-primary mx-1">Year</span><span class="badge badge-primary mx-1">Room Type</span></div>
            <div class="card-body">
                <div class="revenueYearChart">
                    <canvas id="revenueYearChart"></canvas>
                </div>
            </div>

            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Sales</h5>
                        <h6 class="mb-0">RM <span id="salesRevenue"></span></h6>
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
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><span class="mr-2">Room Service Revenue</span><span class="badge badge-primary mx-1">Year</span><span class="badge badge-primary mx-1">Month</span><span class="badge badge-primary mx-1">Room Type</span></div>
            <div class="card-body">
                <div class="roomServiceChart">
                    <canvas id="roomServiceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-header"><span class="mr-2">Room Status (Today)</span><span class="badge badge-primary mx-1">Room Type</span></div>
            <div class="card-body">
                <div class="roomStatusChart">
                    <canvas id="roomStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
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
                @foreach ($years as $year)
                    <option value="{{ $year }}" @if ($year == $yearNow) selected @endif>{{ $year }}</option>
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
<script src="{{ asset("dashboard/plugins/Chart.js/datalabels.min.js") }}"></script>

<script>
    var revenueYearChart = null;
    var roomStatusChart = null;
    var roomServiceChart = null;
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

        function sum(accumulator, value) {
            return accumulator + value;
        }

        function dynamicColor() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
        };

        function generateRevenueYearChart(info, year, roomType) {
            let bookings = info["bookings"];
            let services = info["services"];
            let charges = info["charges"];
            let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
            let totalBooking = bookings.reduce(sum, 0);
            let totalRoomService = services.reduce(sum, 0);
            let totalCharge = charges.reduce(sum, 0);
            let totalRevenue = revenues.reduce(sum, 0);
            $("#salesRevenue").html(totalBooking.toFixed(2));
            $("#roomServiceRevenue").html(totalRoomService.toFixed(2));
            $("#chargeRevenue").html(totalCharge.toFixed(2));
            $("#totalRevenue").html(totalRevenue.toFixed(2));

            let revenueYearCanvas = document.getElementById("revenueYearChart").getContext("2d");
            revenueYearChart = new Chart(revenueYearCanvas, {
                type: 'line',
                data: {
                    labels: Object.values(MONTH),
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
                        displayColors: true,
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
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    }
                }
            });
        }

        function generateRoomStatusChart(info, roomType) {
            let roomStatusCanvas = document.getElementById("roomStatusChart").getContext("2d");
            roomStatusChart = new Chart(roomStatusCanvas, {
                type: 'pie',
                data: {
                    labels: ["Available", "Booked", "Dirty", "Repairing", "Reserved"],
                    datasets: [{
                        backgroundColor: [
                            "{{ App\Models\Room::STATUS[0]["color"] }}",
                            "{{ App\Models\Room::STATUS[1]["color"] }}",
                            "{{ App\Models\Room::STATUS[2]["color"] }}",
                            "{{ App\Models\Room::STATUS[3]["color"] }}",
                            "{{ App\Models\Room::STATUS[4]["color"] }}",
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
                        text: "Room Status (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors: true,
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
                                let percentage = amount / total * 100;
                                return amount + " room" + (amount == 1 ? "" : "s") + " (" + (isFinite(percentage) ? percentage : 0).toFixed(2) + "%)";
                            }
                        }
                    },
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                let dataArr = ctx.chart.data.datasets[0].data;
                                let total = dataArr.reduce(sum, 0);
                                if (total == 0 && ctx.dataIndex == 0)
                                    return "No Data Available";
                                if (value == 0)
                                    return "";
                                let percentage = (value * 100 / total).toFixed(2) + "%";
                                return percentage;
                            },
                            color: 'darkgray',
                            font: {
                                size: 14,
                                weight: "bolder",
                            }
                        }
                    }
                }
            });
        }

        function generateRoomServiceChart(info, year, month, roomType) {
            let labels = info["labels"];
            let data = info["items"];
            let colors = []
            for (let i = 0; i < data.length; i++) {
                colors.push(dynamicColor());
            }
            let roomServiceCanvas = document.getElementById("roomServiceChart").getContext("2d");
            roomServiceChart = new Chart(roomServiceCanvas, {
                type: 'pie',
                data: {
                    labels: info["labels"],
                    datasets: [{
                        data: data,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: (data.length > 10) ? false : true,
                        position: "right",
                        labels: {
                            fontColor: '#ddd',
                            boxWidth: 20
                        }
                    },
                    title: {
                        display: true,
                        text: "Room Service Revenue in " + MONTH[month] + " " + year + " (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors: true,
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
                                let percentage = amount / total * 100;
                                return "RM " + amount + " (" + (isFinite(percentage) ? percentage : 0).toFixed(2) + "%)";
                            }
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: (data.length > 10 && data.reduce(sum, 0) != 0) ? false: true,
                            formatter: (value, ctx) => {
                                let dataArr = ctx.chart.data.datasets[0].data;
                                let total = dataArr.reduce(sum, 0);
                                if (total == 0 && ctx.dataIndex == 0)
                                    return "No Data Available";
                                if (value == 0)
                                    return "";
                                let percentage = (value * 100 / total).toFixed(2) + "%";
                                return percentage;
                            },
                            color: 'darkgray',
                            font: {
                                size: 14,
                                weight: "bolder",
                            }
                        }
                    }
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
            $("#salesRevenue").html(totalBooking.toFixed(2));
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

        function updateRoomStatusChart(info, roomType) {
            roomStatusChart.data.datasets[0].data = info;
            roomStatusChart.options.title.text = "Room Status (" + roomType + ")";
            roomStatusChart.update();
        }

        function updateRoomServiceChart(info, year, month, roomType) {
            let labels = info["labels"];
            let data = info["items"];
            // let colors = []
            // for (let i = 0; i < data.length; i++) {
            //     colors.push(dynamicColor());
            // }
            roomServiceChart.data.labels = labels;
            roomServiceChart.data.datasets[0].data = data;
            // roomServiceChart.data.datasets[0].backgroundColor = colors;
            roomServiceChart.options.title.text = "Room Service Revenue in " + MONTH[month] + " " + year + " (" + roomType + ")";
            roomServiceChart.options.legend.display = (data.length > 10) ? false: true;
            roomServiceChart.options.plugins.datalabels.display = (data.length > 10 && data.reduce(sum, 0) != 0) ? false: true;
            roomServiceChart.update();
        }

        function dateModified(isInitialize) {
            let year = $("#year").val();
            let month = $("#month").val();
            let roomTypeID = $("#roomType").val();
            let roomType = $("#roomType").children("option:selected").html();
            roomType = roomType != "" ? roomType : "All Room Type";
            console.log("ajax call");
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
                        generateRoomStatusChart(response["roomStatusChart"], roomType);
                        generateRoomServiceChart(response["roomServiceChart"], year, month, roomType);
                    }
                    else {
                        updateRevenueYearChart(response["revenueYearChart"], year, roomType);
                        updateRoomStatusChart(response["roomStatusChart"], roomType);
                        updateRoomServiceChart(response["roomServiceChart"], year, month, roomType);
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
