@extends('default')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Create Prize</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'prizes.store', 'id' => 'prizeForm']) !!}
                        <div class="form-group">
                            {!! Form::label('title', 'Title') !!}
                            {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'titleInput']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('probability', 'Probability') !!}
                            {!! Form::number('probability', null, ['class' => 'form-control', 'step' => '0.01', 'id' => 'probabilityInput']) !!}
                        </div>
                        {!! Form::submit('Create', ['class' => 'btn btn-primary', 'id' => 'submitButton']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Centered warning message -->
        <div id="probabilityMessage" class="text-center" style="color: orange; margin-top: 10px;"></div>
    </div>

    <!-- Hidden data element -->
    <div id="prizesData" data-prizes='@json($prizes)'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const prizeForm = document.getElementById('prizeForm');
            const probabilityInput = document.getElementById('probabilityInput');
            const errorMessage = document.getElementById('probabilityMessage');
            const submitButton = document.getElementById('submitButton');
            const prizesDataElement = document.getElementById('prizesData');
            const prizes = JSON.parse(prizesDataElement.getAttribute('data-prizes'));

            function calculateTotalProbability() {
                let total = 0;
                prizes.forEach(prize => {
                    total += parseFloat(prize.probability);
                });
                return total;
            }

            function updateStatus() {
                const inputProbability = parseFloat(probabilityInput.value) || 0;
                const totalProbability = calculateTotalProbability() + inputProbability;
                const remaining = 100 - totalProbability;

                if (totalProbability > 100) {
                    errorMessage.innerHTML = 
                        `Sum of probability 100% currently is ${calculateTotalProbability().toFixed(2)}%. You need to reduce by ${Math.abs(remaining).toFixed(2)}%.`;
                    errorMessage.style.color = 'red';
                    submitButton.disabled = true;
                } else {
                    const formattedTotal = totalProbability.toFixed(2);
                    const formattedRemaining = remaining.toFixed(2);

                    errorMessage.innerHTML = 
                        `Sum of probability 100% currently is ${calculateTotalProbability().toFixed(2)}%. You have yet to add ${formattedRemaining}%.`;
                    errorMessage.style.color = 'orange';
                    submitButton.disabled = false;
                }
            }

            probabilityInput.addEventListener('input', function () {
                updateStatus();
            });

            prizeForm.addEventListener('submit', function (e) {
                const totalProbability = calculateTotalProbability() + (parseFloat(probabilityInput.value) || 0);
                if (totalProbability > 100) {
                    e.preventDefault();
                    errorMessage.innerHTML = 
                        `Sum of probability 100% currently is ${calculateTotalProbability().toFixed(2)}%. You need to reduce by ${Math.abs(100 - totalProbability).toFixed(2)}%.`;
                    errorMessage.style.color = 'red';
                }
            });

            updateStatus();
        });
    </script>
@stop
