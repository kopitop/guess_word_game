@extends('front-end.master')
@section('subview')
    <div class="col-md-4">
        <div class="panel panel-default room-list">
            <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.info') }}</h3>
            <div class="well">
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.description') }}: </strong>{{ $data['room']->description }}</a>
                    <a href="#" class="list-group-item drawer">
                        <strong>{{ trans('front-end/room.player') }}: 
                        <span class="player-name">{{ $data['info']->drawer->name }}</span>
                        </strong>
                    </a>
                    <a href="#" class="list-group-item guesser">
                        <strong>{{ trans('front-end/room.player') }}: 
                        <span class="player-name">{{ $data['info']->guesser->name }}</span>
                        </strong>
                    </a>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.history') }}
                        <span class="pull-right">
                            {!! $data['room']->correctResultsCount()
                            !!}
                            /
                            {!! $data['room']->resultsCount - 1 !!}
                        </span>
                    </a>

                    @foreach ($data['room']->results as $key => $result)
                        @if ($key < $data['room']->resultsCount - 1)
                        <a href="#" class="list-group-item"><i>{{ $result->word->content }}</i>
                            {!! $result->is_correct ?
                                '<span class="pull-right glyphicon glyphicon-ok"></span>' :
                                '<span class="pull-right glyphicon glyphicon-remove"></span>'
                            !!}
                        </a>
                        @elseif (
                            $data['current_round']->isDrawer()  &&
                            (
                                $data['room']->status == config('room.status.playing') ||
                                $data['room']->status == config('room.status.closed')
                            )
                        )
                            <a href="#" class="list-group-item"><i>{{ $result->word->content }}</i>
                            <!-- windows 8 loading effect -->
                                <div class="windows8 pull-right">
                                    <div class="wBall" id="wBall_1">
                                        <div class="wInnerBall"></div>
                                    </div>
                                    <div class="wBall" id="wBall_2">
                                        <div class="wInnerBall"></div>
                                    </div>
                                    <div class="wBall" id="wBall_3">
                                        <div class="wInnerBall"></div>
                                    </div>
                                    <div class="wBall" id="wBall_4">
                                        <div class="wInnerBall"></div>
                                    </div>
                                    <div class="wBall" id="wBall_5">
                                        <div class="wInnerBall"></div>
                                    </div>
                                </div>
                            <!-- end of windows 8 loading effect -->
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="action-room">
                <div class="form-group clearfix">
                    @if (
                        ($data['room']->status == config('room.status.waiting')) || 
                        ($data['room']->status == config('room.status.full'))
                    )
                    <a id="quit-button" class="btn btn-danger" href="javascript:;">
                        {{ trans('front-end/room.buttons.quit') }}
                    </a>
                    <a id="ready-button" class="btn btn-success" href="javascript:;">
                        {{ trans('front-end/room.buttons.ready') }}
                    </a>
                    <input type="hidden" name="ready" id="ready-status" value="0">
                    @elseif ($data['room']->status == config('room.status.playing'))
                    <a id="finish-button" class="btn btn-warning" href="javascript:;">
                        {{ trans('front-end/room.buttons.finish') }}
                    </a> 
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default clearfix">
            <h3>{{ trans('front-end/room.panel') }}</h3>
            @include('layouts.includes.playpanel')
        </div>
    </div>
@endsection
