@extends('default')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Prize</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::model($prize, ['route' => ['prizes.update', $prize->id], 'method' => 'PUT']) !!}
                        <div class="form-group">
                            {!! Form::label('title', 'Title') !!}
                            {!! Form::text('title', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('probability', 'Probability') !!}
                            {!! Form::number('probability', null, ['class' => 'form-control', 'step' => '0.01']) !!}
                        </div>
                        {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
