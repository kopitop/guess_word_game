@extends('front-end.master')

@section('subview')
<div class="col-md-4">
    <div class="panel panel-default room-list">
        <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.list') }}</h3>
        <div class="well">
            <div class="list-group">
                @if (count($rooms))
                @foreach ($rooms as $room)
                    <a href="javascript:;" data-room-id="{{ $room->id }}" class="room-item list-group-item">
                        {{ $room->description }}
                        <span class="pull-right">
                            {{ trans('front-end/room.status.' . $room->status) }}
                        </span>
                    </a>
                @endforeach
                @else
                    <div class="alert alert-warning" role="alert">
                        {{ trans('front-end/room.empty') }}
                    </div>
                @endif
            </div>
        </div>
        
        {{ $rooms->links() }}

        <div class="action-room">
            {!! Form::open([
                'action' => ['Web\RoomsController@store'],
            ]) !!}
                <div class="form-group clearfix">
                    {!! Form::text('description', old('description'), [
                        'class' => 'form-control',
                        'placeholder' => trans('front-end/room.description')
                    ]) !!}
                </div>
                <div class="form-group clearfix">
                    {!! Form::submit(trans('front-end/room.buttons.create'), [
                        'class' => 'btn btn-primary',
                    ]) !!}
                    <a id="join-button" class="btn btn-default">
                        {{ trans('front-end/room.buttons.join') }}
                    </a>
                </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
<div class="col-md-8">
    <div class="panel panel-default">
        <h3>{{ trans('front-end/room.leaderboard') }}</h3>
        {!! $dataTable->table() !!}
    </div>
</div>
<div class="col-md-offset-4 col-md-8">
    <div class="panel panel-default">
        <h3>{{ trans('front-end/room.on-playing') }}</h3>
        <canvas id="myChart" width="400" height="400"></canvas>
    </div>
</div>
@endsection
@push('datatables')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
{!! $dataTable->scripts() !!}
@endpush
@push('script')
<script type="text/javascript">
    var chartData = {};
    chartData.data = [];
    chartData.labels = [];
    chartData.bgColor = [];
    @foreach ($chartData as $item)
        chartData.data.push({{ $item['score'] }});
        chartData.labels.push("{{ trans('front-end/room.title') . $item['room'] }}");
        chartData.bgColor.push("{{ randColor() }}");
    @endforeach
    chartData.trans = {};
    chartData.trans.score = "{{ trans('front-end/room.score') }}";
    chartData.trans.room = "{{ trans('front-end/room.title') }}";
</script>
@endpush

