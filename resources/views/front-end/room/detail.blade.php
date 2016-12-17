@extends('front-end.master')
@push('style')
    {!! Html::style(elixir('css/chat.css')) !!}
@endpush
@section('subview')
@include('front-end.room.includes.chat')
    <div class="alert alert-danger ajax-error"></div>
    <div class="col-md-4">
        <div class="panel panel-default room-list">
            <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.info') }}</h3>
            <div class="well">
                <div class="list-group">
                    <a href="#" class="list-group-item">
                        <strong>{{ trans('front-end/room.description') }}: </strong>
                        {{ $data['room']->description }}
                    </a>
                    <a href="#" class="list-group-item drawer">
                        <strong>{{ trans('front-end/room.player') }}: 
                            <span class="player-name">
                                {{ isset($data['info']->drawer->name) ? $data['info']->drawer->name : '' }}
                            </span>
                            <span class="is-ready"></span>
                        </strong>
                    </a>
                    <a href="#" class="list-group-item guesser">
                        <strong>{{ trans('front-end/room.player') }}: 
                            <span class="player-name">
                                {{ isset($data['info']->guesser->name) ? $data['info']->guesser->name : '' }}
                            </span>
                            <span class="is-ready"></span>
                        </strong>
                    </a>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.history') }}
                        <span class="pull-right">
                            {!! $data['room']->correctResultsCount !!}
                            /
                            {!! $data['room']->resultsCount !!}
                        </span>
                    </a>

                    @foreach ($data['room']->results as $result)
                        @if (!$loop->last)
                        <a href="#" class="list-group-item"><i>{{ $result->word->content }}</i>
                            {!! 
                                $result->is_correct ?
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
                    <!-- render playing panel depend on current round and current room -->
                    @widget('roomInfoPanel', [], $data['room']->status)
                    <div class="ready-block">
                        @if($data['room']->status == config('room.status.full'))
                            <a id="ready-button" class="btn btn-success" href="javascript:;">
                                {{ trans('front-end/room.buttons.ready') }}
                            </a>
                            <input type="hidden" name="ready" id="ready-status" value="0">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default clearfix">
            <h3>{{ trans('front-end/room.panel') }}</h3>
            <h4 id="word">
                {{
                    $data['current_round']->isDrawer() ?
                    $data['current_round']->word_id ?
                    $data['current_round']->word->content : '' : ''
                }}
            </h4>
            <div id="play-panel">
                @include('layouts.includes.playpanel')
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script type="text/javascript">
        var roomId = "{{ $data['room']->id }}";
        var userRole = "{{ $data['current_round']->isDrawer() ? 'drawer' : 'guesser' }}";
        var readyButton = "{{ trans('front-end/room.buttons.ready') }}";
        var playingButton = "{{ trans('front-end/room.buttons.playing') }}";
        var finishButton = "{{ trans('front-end/room.buttons.finish') }}";
        var userId = "{{ Auth::user()->id }}";
        var sendButton = "{{ trans('front-end/room.buttons.send') }}";
        var guesserWaiting = "{{ trans('front-end/room.guesser.waiting') }}";
        var drawerWaiting = "{{ trans('front-end/room.drawer.waiting') }}";
        var roomStatus = "{{ $data['room']->status }}";
        var playingStatus = "{{ config('room.status.playing') }}";
        var fullStatus = "{{ config('room.status.full') }}";
        var authUserID = "{{ Auth::user()->id }}";
        var userName = "{{ Auth::user()->name }}";
        var avatarUser = "{{ Auth::user()->avatar_url ? asset((Auth::user()->avatar_url)) : asset(config('user.default-avatar')) }}";
        var errorMessage = "{{ trans('front-end/room.error-message') }}";
        var placeholderAnswer = "{{ trans('common/placeholders.type-your-answer') }}";
        var imagePath = "{{ config('room.upload-path').$data['room']->id }}";
        var newRoundButton = "{{ trans('front-end/room.buttons.new-round') }}";
    </script>
@endpush
