@extends('front-end.master')
@section('subview')
    <div class="col-md-4">
        <div class="panel panel-default room-list">
            <h3><strong>{{ $title }}: </strong>{{ trans('front-end/room.info') }}</h3>
            <div class="well">
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.description') }}: </strong>{{ $data['room']->description }}</a>
                    <a href="#" class="list-group-item drawer"><strong>{{ trans('front-end/room.player') }}: <span class="player-name"></span></strong></a>
                    <a href="#" class="list-group-item guesser"><strong>{{ trans('front-end/room.player') }}: <span class="player-name"></span></strong></a>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item"><strong>{{ trans('front-end/room.history') }}</a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 1</i><span class="pull-right glyphicon glyphicon-ok"></span></a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 2</i><span class="pull-right glyphicon glyphicon-remove"></span></a>
                    <a href="#" class="list-group-item"><i>{{ trans('front-end/room.word') }} 3</i><span class="pull-right glyphicon glyphicon-remove"></span></a>
                </div>
            </div>

            <div class="action-room">
                <div class="form-group clearfix">
                    <a id="quit-button" class="btn btn-danger" href="javascript:;">{{ trans('front-end/room.buttons.quit') }}</a>
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
                            } else {
                                $('.drawer .player-name').html('');
                            }
                            if (data.guesser !== null) {
                            $('.guesser .player-name').html(data.guesser.name);
                            } else {
                                $('.guesser .player-name').html('');
                            }
                        }
                    });
                }

                //Get new players data when someone joining the room
                socket.on('new-player-connected', refresh)

                //Get new players data when a player quiting the room
                socket.on('a-player-quit', refresh)

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