@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">{{ $user->name }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-auto pt-0 pb-2">
                        <div class="row justify-content-center">
                            <canvas id="chart-bars-user" class="col-md-6 col-xl-6" ></canvas>
                            <canvas id="chart-line-user" class="col-md-6 col-xl-6" ></canvas>
                        </div>
                        <div class="row align-items-center justify-content-center mb-4">
                            <canvas id="chart-donut-user" class="col-md-4 col-xl-6"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    {{-- TODO --}}
    <script>
        var user = @json($user);
        var countVocabulary = @json($countVocabulary);
        var dataUserDaysCountVocReport = @json($dataUserDaysCountVocReport);
        var dataUserDaysCountVocReportArr = dataUserDaysCountVocReport.split(',');
      
        if (dataUserDaysCountVocReportArr.length < 3) {
            for (let i = dataUserDaysCountVocReportArr.length; i < 3; i++) {
                dataUserDaysCountVocReportArr.push(0);
            }
        }
        
        console.log(dataUserDaysCountVocReportArr);
        var ctx = document.getElementById("chart-bars-user").getContext("2d");

        const stackedBar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Day 1", "Day 2", "Day 3", ''],
                datasets: [{
                    data: dataUserDaysCountVocReportArr,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.5)',
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1
                }],
               
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                        ticks: {
                            stepSize: 1,
                            beginAtZero: true
                        },
                        max: 40,
                        ticks: {
                            stepSize: 5
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Knowledge Words Count By Day'
                    }
                }
            },
        });


        var ctx = document.getElementById('chart-donut-user').getContext('2d');
        var know = dataUserDaysCountVocReport.split(',').reduce((a, b) => parseInt(a) + parseInt(b), 0);
        var unKnow = countVocabulary - know; 

        // Tạo biểu đồ mới
        var myDonutChart = new Chart(ctx, {
            type: 'doughnut', // Kiểu biểu đồ là biểu đồ donut
            data: {
                labels: ['Known', 'Remaining'], // Các nhãn trên biểu đồ
                datasets: [{
                    label: 'Dataset 1', // Nhãn cho dữ liệu
                    data: [know, unKnow], // Dữ liệu
                    backgroundColor: [
                        '#20c997',
                        '#cb0c9fa1',
                    ],
                    borderColor: [
                        '#20c997',
                        '#cb0c9f',
                    ],
                    borderWidth: 1 // Độ rộng của đường viền
                }]
            },
            options: {
                responsive: false,
                width: 150,
                height: 150,
                plugins: {
                    legend: {
                        position: 'left',
                    },
                    title: {
                        display: true,
                        text: 'Knowledge Words Rate',
                        position: 'bottom',
                    }
                }
            }
        });

        var ctx = document.getElementById('chart-line-user').getContext('2d');
        var valueBeforeOneDay = 0;
        var percentageValues = dataUserDaysCountVocReport.split(',').map((value) => {
            var total = parseFloat(value) + valueBeforeOneDay;
            valueBeforeOneDay = total;

            var result = (total / parseInt(countVocabulary)) * 100;
            console.log(valueBeforeOneDay, total, result, parseInt(countVocabulary));
            return result.toFixed(2);
        });
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Day 1", "Day 2", "Day 3", ''],
                datasets: [{
                    label: 'Knowledge Words Rate',
                    data: percentageValues,
                    backgroundColor: [
                        'yellow',
                    ],
                    borderColor: [
                        'orange',
                    ],
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%'; // Thêm ký hiệu % vào nhãn trục y
                            }
                        },
                        max: 100
                    }
                },
                animations: {
                    radius: {
                        duration: 400,
                        easing: 'linear',
                        loop: (context) => context.active
                    },
                    tension: {
                        duration: 2000,
                        easing: 'easeInExpo',
                        from: 0.5,
                        to: 0,
                        loop: true
                    },
                },
                hoverRadius: 12,
                hoverBackgroundColor: 'yellow',
                interaction: {
                    mode: 'nearest',
                    intersect: false,
                    axis: 'x',
                    onComplete: () => {
                        delayed = true;
                    },
                    delay: (context) => {
                        let delay = 0;
                        if (context.type === 'data' && context.mode === 'default' && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                        }
                        return delay;
                    },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Total Knowledge Words Rate By Day'
                    }
                }
            },
        });
    </script>
    @endpush
@endsection
