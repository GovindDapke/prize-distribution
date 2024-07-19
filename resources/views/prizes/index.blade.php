@extends('default')

@section('content')

    @include('prob-notice')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate'], 'id' => 'simulateForm']) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', 50, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>
                    <br>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['reset'], 'id' => 'resetForm']) !!}
                        {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chartData" data-prizes='@json($prizes)'></div>

    <div class="container mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <div class="chart-container" style="position: relative; height:60vh;">
                    <canvas id="probabilityChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <div class="chart-container" style="position: relative; height:60vh;">
                    <canvas id="awardedChart"></canvas>
                </div>
            </div>
        </div>
    </div>

@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartDataElement = document.getElementById('chartData');
            const prizes = JSON.parse(chartDataElement.getAttribute('data-prizes'));

            const prizeTitles = prizes.map(prize => prize.title);
            const prizeProbabilities = prizes.map(prize => prize.probability);
            const awardedCounts = prizes.map(prize => prize.awarded_count);
            const backgroundColors = ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56'];

            const createChart = (ctx, label, data) => {
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: prizeTitles,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: backgroundColors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                display: false, // Disable internal labels
                                formatter: (value, ctx) => {
                                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    let percentage = (value * 100 / sum).toFixed(2) + "%";
                                    let label = ctx.chart.data.labels[ctx.dataIndex];
                                    return `${label}: ${percentage}`;
                                },
                                color: '#fff',
                                anchor: 'center',
                                align: 'center',
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                padding: 8
                            },
                            legend: {
                                display: true,
                                position: 'right', // Position legend to the right
                                labels: {
                                    generateLabels: (chart) => {
                                        const data = chart.data;
                                        return data.labels.map((label, index) => ({
                                            text: `${label}: ${data.datasets[0].data[index]} (${(data.datasets[0].data[index] * 100 / data.datasets[0].data.reduce((a, b) => a + b, 0)).toFixed(2)}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[index],
                                            strokeStyle: data.datasets[0].backgroundColor[index],
                                            lineWidth: 1
                                        }));
                                    }
                                }
                            }
                        },
                        cutout: '50%', // Adjust this to control the size of the inner circle
                        elements: {
                            arc: {
                                borderWidth: 1 // Adjust this if needed
                            }
                        }
                    }
                });
            };

            const probabilityChart = createChart(document.getElementById('probabilityChart').getContext('2d'), 'Probability', prizeProbabilities);
            const awardedChart = createChart(document.getElementById('awardedChart').getContext('2d'), 'Awarded Count', awardedCounts);

            const handleFormSubmission = (formId, route, chartUpdater) => {
                document.getElementById(formId).addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetch(route, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: new URLSearchParams(new FormData(this))
                    })
                    .then(response => response.json())
                    .then(data => {
                        chartUpdater(data.prizes);
                    });
                });
            };

            const updateCharts = (prizes) => {
                const prizeTitles = prizes.map(prize => prize.title);
                const prizeProbabilities = prizes.map(prize => prize.probability);
                const awardedCounts = prizes.map(prize => prize.awarded_count);

                probabilityChart.data.labels = prizeTitles;
                probabilityChart.data.datasets[0].data = prizeProbabilities;
                probabilityChart.update();

                awardedChart.data.labels = prizeTitles;
                awardedChart.data.datasets[0].data = awardedCounts;
                awardedChart.update();
            };

            handleFormSubmission('simulateForm', "{{ route('simulate') }}", updateCharts);
            handleFormSubmission('resetForm', "{{ route('reset') }}", updateCharts);
        });
    </script>
@endpush
