@extends("customer.layouts.template")

@push("css")
<style>
    .spent-chart,
    .booking-chart {
        position: relative;
        height: 350px;
    }
</style>
@endpush

@section("title")
    Statistics
@endsection

@section("title2")
    Statistics
@endsection

@php
    $now = Carbon\Carbon::now();
    $yearNow = $now->year;
    $monthNow = $now->month;
@endphp

@section("content")
<div class="row mt-3">
    <div class="col-4 form-group">
        <div class="input-group">
            <label for="year">Year</label>
            <select class="wide niceSelect border-dark" id="year">
                @foreach ($years as $year)
                    <option value="{{ $year }}" @if ($year == $yearNow) selected @endif>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="mr-2">Total Spents</span></div>
            <div class="card-body">
                <div class="spent-chart">
                    <canvas id="spent-chart"></canvas>
                </div>
            </div>
            <div class="row m-0 row-group text-center border-top">
                <div class="col-3 border-right">
                    <div class="p-3">
                        <h5 class="mb-2">Booking Spents</h5>
                        <h6 class="mb-0">RM <span id="booking-spent"></span></h6>
                    </div>
                </div>
                <div class="col-3 border-right">
                    <div class="p-3">
                        <h5 class="mb-2">Room Services</h5>
                        <h6 class="mb-0">RM <span id="room-service-spent"></span></h6>
                    </div>
                </div>
                <div class="col-3 border-right">
                    <div class="p-3">
                        <h5 class="mb-2">Charges</h5>
                        <h6 class="mb-0">RM <span id="charge-spent"></span></h6>
                    </div>
                </div>
                <div class="col-3 border-right">
                    <div class="p-3">
                        <h5 class="mb-2">Total Spents</h5>
                        <h6 class="mb-0">RM <span id="total-spent"></span></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="mr-2">Total Number of Booking</span></div>
            <div class="card-body">
                <div class="booking-chart">
                    <canvas id="booking-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("script")
    <script>
        $(document).ready(function () {
            var spentChart = null;
            var bookingChart = null;
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

            function generateSpentChart(info, year) {
                let bookings = info["bookings"];
                let services = info["services"];
                let charges = info["charges"];
                let spents = bookings.map((value, index) => value + services[index] + charges[index]);
                let totalBooking = bookings.reduce(sum, 0);
                let totalRoomService = services.reduce(sum, 0);
                let totalCharge = charges.reduce(sum, 0);
                let totalSpents = spents.reduce(sum, 0);
                $("#booking-spent").html(totalBooking.toFixed(2));
                $("#room-service-spent").html(totalRoomService.toFixed(2));
                $("#charge-spent").html(totalCharge.toFixed(2));
                $("#total-spent").html(totalSpents.toFixed(2));

                let spentChartCanvas = document.getElementById("spent-chart").getContext("2d");
                spentChart = new Chart(spentChartCanvas, {
                    type: 'line',
                    data: {
                        labels: Object.values(MONTH),
                        datasets: [
                            {
                                label: 'Total Spents',
                                data: spents,
                                backgroundColor: "darkgray",
                                borderColor: "darkgray",
                                borderWidth: 0,
                                fill: false,
                            },
                            {
                                label: 'Booking Spents',
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
                                fontColor: 'black',
                                boxWidth: 20
                            }
                        },
                        title: {
                            display: true,
                            text: "Spending Chart in Year " + year,
                            fontColor: "black",
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
                                    beginAtZero: true,
                                    fontColor: 'black'
                                },
                                gridLines: {
                                    display: true,
                                    color: "rgba(221, 221, 221, 0.08)"
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                    fontColor: "black",
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true,
                                    fontColor: 'black',
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
                                    labelString: 'Spending',
                                    fontColor: "black",
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

            function generateBookingChart(bookings, year) {
                let bookingChartCanvas = document.getElementById("booking-chart").getContext("2d");
                bookingChart = new Chart(bookingChartCanvas, {
                    type: 'line',
                    data: {
                        labels: Object.values(MONTH),
                        datasets: [
                            {
                                label: 'Number of Bookings',
                                data: bookings,
                                backgroundColor: "blue",
                                borderColor: "blue",
                                borderWidth: 0,
                                fill: false,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: "right",
                            labels: {
                                fontColor: 'black',
                                boxWidth: 20
                            }
                        },
                        title: {
                            display: true,
                            text: "Booking Chart in Year " + year,
                            fontColor: "black",
                        },
                        tooltips: {
                            displayColors: true,
                            callbacks: {
                                label: function (context, constant) {
                                    return context.yLabel + " bookings";
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: 'black'
                                },
                                gridLines: {
                                    display: true,
                                    color: "rgba(221, 221, 221, 0.08)"
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                    fontColor: "black",
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true,
                                    fontColor: 'black',
                                    callback: function(value, index, values) {
                                        if (Math.floor(value) === value) {
                                            return value;
                                        }
                                    }
                                },
                                gridLines: {
                                    display: true,
                                    color: "rgba(221, 221, 221, 0.08)"
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Number of Booking',
                                    fontColor: "black",
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

            function updateSpentChart(info, year, roomType) {
                let bookings = info["bookings"];
                let services = info["services"];
                let charges = info["charges"];
                let spents = bookings.map((value, index) => value + services[index] + charges[index]);
                let totalBooking = bookings.reduce(sum, 0);
                let totalRoomService = services.reduce(sum, 0);
                let totalCharge = charges.reduce(sum, 0);
                let totalRevenue = spents.reduce(sum, 0);
                $("#booking-spent").html(totalBooking.toFixed(2));
                $("#room-service-spent").html(totalRoomService.toFixed(2));
                $("#charge-spent").html(totalCharge.toFixed(2));
                $("#total-spent").html(totalRevenue.toFixed(2));
                spentChart.data.datasets[0].data = spents;
                spentChart.data.datasets[1].data = bookings;
                spentChart.data.datasets[2].data = services;
                spentChart.data.datasets[3].data = charges;
                spentChart.options.title.text = "Revenue Chart in Year " + year;
                spentChart.update();
            }

            function updateBookingChart(info, year) {
                bookingChart.data.datasets[0].data = info;
                bookingChart.options.title.text = "Booking Chart in Year " + year;
                bookingChart.update();
            }

            function dateModified(isInitialize) {
                let year = $("#year").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route("customer.analysis.json") }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "year": year,
                    },
                    success: function (response) {
                        year = parseInt(year);

                        if (isInitialize) {
                            generateSpentChart(response["spentChart"], year);
                            generateBookingChart(response["bookingChart"], year);
                        }
                        else {
                            updateSpentChart(response["spentChart"], year);
                            updateBookingChart(response["bookingChart"], year);
                        }
                    }
                });
            }

            $("#year").on("change", function() {
                dateModified(false);
            });

            dateModified(true);
        });
    </script>
@endpush
