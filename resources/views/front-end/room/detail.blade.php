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
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script>
        (function() {
            //Checkif jQuery has been initialized
            var runMyCode = function($) {
                //Init a socket
                var socket = io('http://localhost:3000');

                //Joined a room
                var room = "room-" + "{{ $data['room']->id }}";
                socket.on('connect', function (data) {
                    socket.emit('joined', room);
                });

                //Get new players data when someone joining the room
                socket.on('new-player-connected', function () {
                    var url = '/rooms/refresh';
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
                            $('.drawer .player-name').html(data.drawer.name);
                            $('.guesser .player-name').html(data.guesser.name);
                            console.log(data);
                        }
                    });
                })
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