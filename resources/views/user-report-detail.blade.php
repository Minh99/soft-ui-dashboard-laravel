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
                    <div class="card-body px-0 pt-0 pb-2">
                        <div id="chart-bars-user">

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
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("chart-bars-user").getContext("2d");

            const stackedBar = new Chart(ctx, {
                type: 'bar',
                labels: ["Day 1", "Day 2", "Day 3", "Day 4", "Day 5", "Day 6"],
                data: {
                    datasets: [{
                        barPercentage: 0.5,
                        barThickness: 6,
                        maxBarThickness: 8,
                        minBarLength: 2,
                        data: [10, 20, 30, 40, 50, 60]
                    }]
                },
                options: {
                    scales: {
                    y: {
                        beginAtZero: true
                    }
                    }
                },
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            });
        });
    </script>
    @endpush
@endsection
