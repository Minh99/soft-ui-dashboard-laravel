@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <canvas id="vocabChart" width="400" height="200"></canvas>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2 mt-4">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-0">
                                            Name
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-0">
                                            Email
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Detail Report
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="row justify-content-center align-items-center">
                                                    <p class="text-sm font-weight-bold mb-0 col-md-6 m-auto">{{  $item['id'] }}</p>
                                                    <p class="col-md-6 text-center m-auto" style="background: {{ $item['color'] }}; max-height: 10px; max-width: 15px">&nbsp;</p>
                                                </div>
                                            </td>
                                            <td class="px-0">
                                                <p class="text-xs font-weight-bold mb-0">{{ $item['name'] }}</p>
                                            </td>
                                            <td class="px-0">
                                                <p class="text-xs font-weight-bold mb-0">{{ $item['email'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <a class="text-primary text-xs" href="{{ route('user-report-detail', ['id' => $item['id']]) }}">
                                                    View detail <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        var students = @json($users);
        console.log(students);

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        const labels = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6'];

        // convert students object to datasets array

        const datasets = Object.entries(students).map(([student, data]) => {
            console.log(data);
            var vocabData = data['dataUserDaysCountVocReport']
            var vocabDataArr = vocabData.split(',');
            if (vocabDataArr.length < 6) {
                for (let i = vocabDataArr.length; i < 6; i++) {
                    vocabDataArr.push(0);
                }
            }
            return {
                label: data['name'],
                data: vocabDataArr,
                borderColor: data['color'] ?? getRandomColor(),
                backgroundColor: data['color'] ?? getRandomColor(),
                fill: false
            };
        });

        console.log(datasets);


        const data = {
            labels: labels,
            datasets: datasets
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'User Vocabulary Report'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                animations: {
                    tension: {
                        duration: 5000,
                        easing: 'easeInExpo',
                        from: 0.5,
                        to: 0,
                        loop: true
                    },
                },
                hoverRadius: 12,
                hoverBackgroundColor: getRandomColor(),
            }
        };

        const ctx = document.getElementById('vocabChart').getContext('2d');
        const vocabChart = new Chart(ctx, config);
    </script>
    @endpush
@endsection
