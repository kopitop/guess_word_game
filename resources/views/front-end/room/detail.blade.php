@extends('front-end.master')
@section('subview')
    <div class="col-md-4">
        <div class="panel panel-default room-list">
            <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.info') }}</h3>
            <div class="well">
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.description') }}: </strong>{{ $data['room']->description }}</a>
                    <a href="#" class="list-group-item drawer"><strong>{{ trans('front-end/room.player') }}: <span class="player-name"></span><span class="is-ready"></span></strong></a>
                    <a href="#" class="list-group-item guesser"><strong>{{ trans('front-end/room.player') }}: <span class="player-name"></span><span class="is-ready"></span></strong></a>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.history') }}
                        <span class="pull-right">
                            {!! $data['results']->filter(function ($value, $key) {
                                return $value->is_correct;
                            })->count();
                            !!}
                            /
                            {!! count($data['results']) - 1 !!}
                        </span>
                    </a>

                    @foreach ($data['results'] as $key => $result)
                        @if ($key < count($data['results']) - 1)
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
                    <a id="quit-button" class="btn btn-danger" href="javascript:;">{{ trans('front-end/room.buttons.quit') }}</a>
                    <a id="ready-button" class="btn btn-success" href="javascript:;">{{ trans('front-end/room.buttons.ready') }}</a>
                    <input type="hidden" name="ready" id="ready-status" value="0">
                    @elseif ($data['room']->status == config('room.status.playing'))
                    <a id="finish-button" class="btn btn-warning" href="javascript:;">{{ trans('front-end/room.buttons.finish') }}</a> 
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default clearfix">
            <h3>{{ trans('front-end/room.panel') }}</h3>
            @include('layouts.chatbox')
        </div>
    </div>
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script>
        (function() {
            //Checkif jQuery has been initialized
            var runMyCode = function($) {
                //Init a socket
                var socket = io('http://localhost:3000', {
                    'reconnectionAttempts': 3,
                });

                //Joined a room
                var room = "room-" + "{{ $data['room']->id }}";
                socket.on('connect', function (data) {
                    socket.emit('joined', room);
                });

                //Refresh room function
                function refresh() {
                    var url = '/rooms/refresh';
                    //Inject csrf-token to ajax request
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        method: 'POST', 
                        url: url,
                        data: {id: "{{ $data['room']->id }}"},
                        dataType: 'JSON',
                        success: function (data, textStatus, jqXHR) {
                            if (data.drawer !== null) {
                                $('.drawer .player-name').html(data.drawer.name);
                                $('.drawer').data('userid', data.drawer.id);
                            } else {
                                $('.drawer .player-name').html('');
                            }

                            if (data.guesser !== null) {
                                $('.guesser .player-name').html(data.guesser.name);
                                $('.guesser').data('userid', data.guesser.id);
                            } else {
                                $('.guesser .player-name').html('');
                            }
                            $('.is-ready').html('');
                        }
                    });
                }

                //Get new players data when someone joining the room
                socket.on('new-player-connected', refresh);

                //Get new players data when a player quiting the room
                socket.on('a-player-quit', refresh);

                //Quit button
                $('#quit-button').on('click', function () {
                    var url = '/rooms/quit'
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}"},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            socket.emit('quit', room);
                            window.location.replace("/rooms");
                        }
                    });
                });


                //Ready button
                $('#ready-button').on('click', function () {
                    $('#ready-status').val(($('#ready-status').val() == 1) ? 0 : 1);
                    socket.emit('ready', parseInt($('#ready-status').val()), room, "{{ Auth::user()->id }}");
                    var url = '/rooms/ready'
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}", ready: parseInt($('#ready-status').val())},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            if (data == 15) {
                                socket.emit('all-ready', room);
                            }
                        }
                    });
                });

                //Updating status of player
                socket.on('a-player-click-ready', function (ready, userid) {
                    if ($('.drawer').data('userid') == userid && ready == 1) {
                        $('.drawer .is-ready').append('<button class="btn btn-info btn-sm pull-right">Ready</button>');
                    } else if ($('.drawer').data('userid') == userid && ready == 0) {
                        $('.drawer .is-ready').text('');
                    }

                    if ($('.guesser').data('userid') == userid && ready == 1) {
                        $('.guesser .is-ready').append('<button class="btn btn-info btn-sm pull-right">Ready</button>');
                    } else if ($('.guesser').data('userid') == userid && ready == 0) {
                        $('.guesser .is-ready').text('');
                    }
                })

                //Initialize first round
                socket.on('start-to-play', function (ready, userid) {
                    var url = '/rooms/start-to-play'
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}"},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            if ("{{ Auth::user()->id }}" == $('.drawer').data('userid')) {
                                $('#word').html(data.word.content);
                            } else {
                                $('#wPaint').hide();
                                $('#word').html('Please waiting...');
                            }
                            $('.is-ready').html('<button class="btn btn-success btn-sm pull-right">Playing</button>');
                            $('#ready-button').hide();
                        }
                    });
                })

                //Send image
                $('#send-image').on('click', function (){
                    var url = '/rooms/send-image';
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}", image:$('#wPaint').wPaint('image')},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            socket.emit('post-image', data, room);
                        }
                    });
                });

                //Render image
                socket.on('render-image', function (data) {
                    if ("{{ $data['current_round']->isDrawer() }}") {
                        $('#wPaint').hide();
                        $('#word').append('Please waiting...');
                    } else {
                        $('.chat_area').append('<img id="image" src="' + data.current_round.image + '">');
                        $('.chat_area').append('<input id="answer" type="text" name="answer" class="form-control" placeholder="Type your answer">');
                        $('.chat_area').append('<a id="submit-answer" href="javascript:;" class="pull-right btn btn-success">{{ trans('front-end/room.buttons.submit') }}</a>');
                    }
                });

                //Submit answer
                $(document).on('click', '#submit-answer', function (){
                    var url = '/rooms/submit-answer';
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}", answer: $('#answer').val()},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            socket.emit('post-answer', data, room);
                        }
                    });
                });

                //Render result
                socket.on('render-result', function (data) {
                    $('#answer').remove();
                    $('#image').remove();
                    $('#send-image').remove();
                    $('#submit-answer').remove();
                    $('#result').html('Answer of guesser is ' + data.current_round.answer + ' ,and the true answer is' + " {{ $data['current_round']->word ? $data['current_round']->word->content : '' }}");
                    if ("{{ $data['current_round']->isDrawer() }}") {
                        $('#result').append('<a href="javascript:;" id="new-round" class="btn btn-primary">{{ trans('front-end/room.buttons.new-round') }}</a>');
                    }
                });

                //New round
                $(document).on('click', '#new-round', function () {
                    var url = '/rooms/new-round';
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}"},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            socket.emit('new-round', room);
                        }
                    });
                });

                //Get new round
                socket.on('get-new-round', function () {
                    location.reload();
                });

                //When a player click finish, we'll close the room
                $(document).on('click', '#finish-button', function () {
                    var url = '/rooms/finish';
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: {id: "{{ $data['room']->id }}"},
                        dataType: 'json',
                        success: function (data, textStatus, jqXHR) {
                            socket.emit('finish', room);
                        }
                    });
                });

                //After closing room, we need to refresh page of players
                socket.on('close-room', function () {
                    location.reload();
                });
            };

            var timer = function() {
                if (window.jQuery && window.jQuery.ui) {
                    runMyCode(window.jQuery);
                } else {
                    window.setTimeout(timer, 100);
                }
            };
            timer();
        })();
    </script>
@endsection