@extends("dashboard.layouts.template")

@push("css")
<style>
    .revenueYearChart,
    .roomStatusChart,
    .roomServiceChart,
    .occupancyRateChart,
    .averageRoomRateChart {
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
            <div class="card-header"><span class="mr-2">Revenue</span><span class="badge badge-primary mx-1">Year</span></div>
            <div class="card-body">
                <div class="revenueYearChart">
                    <canvas id="revenueYearChart"></canvas>
                </div>
            </div>
            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Room Revenues</h5>
                        <h6 class="mb-0">RM <span id="sales-revenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Room Services</h5>
                        <h6 class="mb-0">RM <span id="room-service-revenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Additional Charges</h5>
                        <h6 class="mb-0">RM <span id="charge-revenue"></span></h6>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3">
                        <h5 class="mb-2">Total Revenues</h5>
                        <h6 class="mb-0">RM <span id="total-revenue"></span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><span class="mr-2">Room Service Revenue</span><span class="badge badge-primary mx-1">Year</span><span class="badge badge-primary mx-1">Month</span></div>
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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="mr-2">Hotel Room Occupancy</span><span class="badge badge-primary mx-1">Year</span><span class="badge badge-primary mx-1">Room Type</span></div>
            <div class="card-body">
                <div class="occupancyRateChart">
                    <canvas id="occupancyRateChart"></canvas>
                </div>
            </div>
            <div class="row m-0 row-group text-center border-top border-light-3">
                <div class="col-4">
                    <div class="p-3">
                        <h5 class="mb-2">Total Nights</h5>
                        <h6 class="mb-0"><span id="total-nights"></span> nights</h6>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <h5 class="mb-2">Nights Occupied</h5>
                        <h6 class="mb-0"><span id="nights-occupied"></span> nights</h6>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <h5 class="mb-2">Occupancy Rate</h5>
                        <h6 class="mb-0"><span id="occupancy-rate"></span>%</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="mr-2">Average Room Rate (ARR)</span><span class="badge badge-primary mx-1">Year</span><span class="badge badge-primary mx-1">Room Type</span></div>
            <div class="card-body">
                <div class="averageRoomRateChart">
                    <canvas id="averageRoomRateChart"></canvas>
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
    var occupancyRateChart = null;
    var averageRoomRateChart = null;
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

        function generateRevenueYearChart(info, year) {
            let bookings = info["bookings"];
            let services = info["services"];
            let charges = info["charges"];
            let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
            let totalBooking = bookings.reduce(sum, 0);
            let totalRoomService = services.reduce(sum, 0);
            let totalCharge = charges.reduce(sum, 0);
            let totalRevenue = revenues.reduce(sum, 0);
            $("#sales-revenue").html(totalBooking.toFixed(2));
            $("#room-service-revenue").html(totalRoomService.toFixed(2));
            $("#charge-revenue").html(totalCharge.toFixed(2));
            $("#total-revenue").html(totalRevenue.toFixed(2));

            let revenueYearCanvas = document.getElementById("revenueYearChart").getContext("2d");
            revenueYearChart = new Chart(revenueYearCanvas, {
                type: 'line',
                data: {
                    labels: Object.values(MONTH),
                    datasets: [
                        {
                            label: 'Total Revenues',
                            data: revenues,
                            backgroundColor: "darkgray",
                            borderColor: "darkgray",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Room Revenues',
                            data: bookings,
                            backgroundColor: "blue",
                            borderColor: "blue",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Room Services',
                            data: services,
                            backgroundColor: "yellow",
                            borderColor: "yellow",
                            borderWidth: 0,
                            fill: false,
                        },
                        {
                            label: 'Additional Charges',
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
                        text: "Revenue Chart in Year " + year,
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
                    labels: ["Available", "Booked", "Dirty", "Repairing", "Occupied", "Cleaning"],
                    datasets: [{
                        backgroundColor: [
                            "{{ App\Models\Room::STATUS[0]["color"] }}",
                            "{{ App\Models\Room::STATUS[1]["color"] }}",
                            "{{ App\Models\Room::STATUS[2]["color"] }}",
                            "{{ App\Models\Room::STATUS[3]["color"] }}",
                            "{{ App\Models\Room::STATUS[4]["color"] }}",
                            "{{ App\Models\Room::STATUS[5]["color"] }}",
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
                                return amount + " rooms" + " (" + (percentage || 0).toFixed(2) + "%)";
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

        function generateRoomServiceChart(info, year, month) {
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
                        text: "Room Service Revenue in " + MONTH[month] + " " + year,
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
                                return "RM " + amount.toFixed(2) + " (" + (percentage || 0).toFixed(2) + "%)";
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

        function generateOccupancyRateChart(info, year, roomType) {
            let roomsCount = info["roomsCount"];
            let totalRooms = [];
            let occupiedRooms = info["occupied"];
            for (let i = 1; i <= 12; i++) {
                totalRooms.push(new Date(year, i, 0).getDate() * roomsCount);
            }
            let percentage = occupiedRooms.map((value, index) => value * 100 / totalRooms[index]);
            let totalAvailableRooms = totalRooms.reduce(sum, 0);
            let totalOccupiedRooms = occupiedRooms.reduce(sum, 0);
            let totalPercentage = totalOccupiedRooms / totalAvailableRooms * 100;

            $("#total-nights").html(totalAvailableRooms);
            $("#nights-occupied").html(totalOccupiedRooms);
            $("#occupancy-rate").html(totalPercentage.toFixed(2));

            let occupancyRateCanvas = document.getElementById("occupancyRateChart").getContext("2d");
            occupancyRateChart = new Chart(occupancyRateCanvas, {
                type: 'line',
                data: {
                    labels: Object.values(MONTH),
                    datasets: [
                        {
                            label: 'Occupancy Rate',
                            data: percentage,
                            occupiedRooms: occupiedRooms,
                            totalRooms: totalRooms,
                            backgroundColor: "lightblue",
                            borderColor: "lightblue",
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
                        text: "Hotel Occupancy in Year " + year + " (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors: true,
                        callbacks: {
                            label: function (context, constant) {
                                let datasetIndex = context.datasetIndex;
                                let index = context.index;
                                return [
                                    "Occupied Rooms: " + constant.datasets[datasetIndex].occupiedRooms[index],
                                    "Total Rooms: " + constant.datasets[datasetIndex].totalRooms[index],
                                    "Occupancy Rate: " + context.yLabel.toFixed(2) + "%"
                                ]
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
                                max: 100,
                                stepSize: 20,
                                fontColor: '#ddd',
                                callback: function(value, index, values) {
                                    return value + "%";
                                }
                            },
                            gridLines: {
                                display: true,
                                color: "rgba(221, 221, 221, 0.08)"
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Occupancy Rate',
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

        function generateAverageRoomRateChart(data, year, roomType) {
            let roomRevenues = data["roomRevenue"];
            let roomSold = data["roomSold"];
            let averageRoomRate = roomRevenues.map((value, index) => value / roomSold[index] || 0);

            let averageRoomRateCanvas = document.getElementById("averageRoomRateChart").getContext("2d");
            averageRoomRateChart = new Chart(averageRoomRateCanvas, {
                type: 'line',
                data: {
                    labels: Object.values(MONTH),
                    datasets: [
                        {
                            label: 'Average Room Rate',
                            data: averageRoomRate,
                            roomRevenues: roomRevenues,
                            roomSold: roomSold,
                            backgroundColor: "yellow",
                            borderColor: "yellow",
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
                        text: "Average Room Rate (ARR) in Year " + year + " (" + roomType + ")",
                        fontColor: "white",
                    },
                    tooltips: {
                        displayColors: true,
                        callbacks: {
                            label: function (context, constant) {
                                let datasetIndex = context.datasetIndex;
                                let index = context.index;
                                return [
                                    "Room Revenues: RM " + constant.datasets[datasetIndex].roomRevenues[index].toFixed(2),
                                    "Room Sold: " + constant.datasets[datasetIndex].roomSold[index],
                                    "Average Room Rate: RM " + context.yLabel.toFixed(2)
                                ]
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
                                    return "RM " + value;
                                }
                            },
                            gridLines: {
                                display: true,
                                color: "rgba(221, 221, 221, 0.08)"
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Average Room Rate',
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

        function updateRevenueYearChart(info, year) {
            let bookings = info["bookings"];
            let services = info["services"];
            let charges = info["charges"];
            let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
            let totalBooking = bookings.reduce(sum, 0);
            let totalRoomService = services.reduce(sum, 0);
            let totalCharge = charges.reduce(sum, 0);
            let totalRevenue = revenues.reduce(sum, 0);
            $("#sales-revenue").html(totalBooking.toFixed(2));
            $("#room-service-revenue").html(totalRoomService.toFixed(2));
            $("#charge-revenue").html(totalCharge.toFixed(2));
            $("#total-revenue").html(totalRevenue.toFixed(2));
            revenueYearChart.data.datasets[0].data = revenues;
            revenueYearChart.data.datasets[1].data = bookings;
            revenueYearChart.data.datasets[2].data = services;
            revenueYearChart.data.datasets[3].data = charges;
            revenueYearChart.options.title.text = "Revenue Chart in Year " + year;
            revenueYearChart.update();
        }

        function updateRoomStatusChart(info, roomType) {
            roomStatusChart.data.datasets[0].data = info;
            roomStatusChart.options.title.text = "Room Status (" + roomType + ")";
            roomStatusChart.update();
        }

        function updateRoomServiceChart(info, year, month) {
            let labels = info["labels"];
            let data = info["items"];
            roomServiceChart.data.labels = labels;
            roomServiceChart.data.datasets[0].data = data;
            roomServiceChart.options.title.text = "Room Service Revenue in " + MONTH[month] + " " + year;
            roomServiceChart.options.legend.display = (data.length > 10) ? false: true;
            roomServiceChart.options.plugins.datalabels.display = (data.length > 10 && data.reduce(sum, 0) != 0) ? false: true;
            roomServiceChart.update();
        }

        function updateOccupancyRateChart(info, year, roomType) {
            let roomsCount = info["roomsCount"];
            let totalRooms = [];
            let occupiedRooms = info["occupied"];
            for (let i = 1; i <= 12; i++) {
                totalRooms.push(new Date(year, i, 0).getDate() * roomsCount);
            }
            let percentage = occupiedRooms.map((value, index) => value * 100 / totalRooms[index])
            let totalAvailableRooms = totalRooms.reduce(sum, 0);
            let totalOccupiedRooms = occupiedRooms.reduce(sum, 0);
            let totalPercentage = totalOccupiedRooms / totalAvailableRooms * 100;

            $("#total-nights").html(totalAvailableRooms);
            $("#nights-occupied").html(totalOccupiedRooms);
            $("#occupancy-rate").html(totalPercentage.toFixed(2));

            occupancyRateChart.data.datasets[0].data = percentage;
            occupancyRateChart.data.datasets[0].occupiedRooms = occupiedRooms;
            occupancyRateChart.data.datasets[0].totalRooms = totalRooms;
            occupancyRateChart.options.title.text = "Hotel Occupancy in Year " + year + " (" + roomType + ")",
            occupancyRateChart.update();
        }

        function updateAverageRoomRateChart(data, year, roomType) {
            let roomRevenues = data["roomRevenue"];
            let roomSold = data["roomSold"];
            let averageRoomRate = roomRevenues.map((value, index) => value / (roomSold[index] || 1));
            averageRoomRateChart.data.datasets[0].data = averageRoomRate;
            averageRoomRateChart.data.datasets[0].roomSold = roomSold;
            averageRoomRateChart.data.datasets[0].roomRevenues = roomRevenues;
            averageRoomRateChart.options.title.text = "Average Room Rate (ARR) in Year " + year + " (" + roomType + ")",
            averageRoomRateChart.update();
        }

        function dateModified(isInitialize) {
            let year = $("#year").val();
            let month = $("#month").val();
            let roomTypeID = $("#roomType").val();
            let roomType = $("#roomType").children("option:selected").html();
            roomType = roomType != "" ? roomType : "All Room Type";
            $.ajax({
                type: "POST",
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
                    let bookings = response["revenueYearChart"]["bookings"];
                    let services = response["revenueYearChart"]["services"];
                    let charges = response["revenueYearChart"]["charges"];
                    let revenues = bookings.map((value, index) => value + services[index] + charges[index]);
                    let occupiedRooms = response["occupancyRateChart"]["occupied"];
                    if (isInitialize) {
                        generateRevenueYearChart(response["revenueYearChart"], year);
                        generateRoomStatusChart(response["roomStatusChart"], roomType);
                        generateRoomServiceChart(response["roomServiceChart"], year, month);
                        generateOccupancyRateChart(response["occupancyRateChart"], year, roomType);
                        generateAverageRoomRateChart(response["averageRoomRateChart"], year, roomType);
                    }
                    else {
                        updateRevenueYearChart(response["revenueYearChart"], year);
                        updateRoomStatusChart(response["roomStatusChart"], roomType);
                        updateRoomServiceChart(response["roomServiceChart"], year, month);
                        updateOccupancyRateChart(response["occupancyRateChart"], year, roomType);
                        updateAverageRoomRateChart(response["averageRoomRateChart"], year, roomType);
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
