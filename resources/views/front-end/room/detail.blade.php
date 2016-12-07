@extends('front-end.master')
@section('subview')
    <div class="col-md-4">
        <div class="panel panel-default room-list">
            <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.info') }}</h3>
            <div class="well">
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.description') }}: </strong>{{ $room['room']->description }}</a>
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.player') }}: {{ $room['result']->drawer_id  ? $room['result']->drawer->name : '' }}</strong></a>
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.player') }}: {{ $room['result']->guesser_id  ? $room['result']->guesser->name : '' }}</strong></a>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.history') }}</a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 1</i><span class="pull-right glyphicon glyphicon-ok"></span></a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 2</i><span class="pull-right glyphicon glyphicon-remove"></span></a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 3</i><span class="pull-right glyphicon glyphicon-remove"></span></a>
                </div>
            </div>

            <div class="action-room">
                {!! Form::open([
                    'action' => ['Web\RoomsController@quit'],
                ]) !!}
                    <div class="form-group clearfix">
                        {!! Form::submit(trans('front-end/room.buttons.quit'), [
                            'class' => 'btn btn-danger',
                        ]) !!}
                    </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default clearfix">
            <h3>{{ trans('front-end/room.panel') }}</h3>
            @include('layouts.chatbox')
        </div>
    </div>
@endsection