(function() {
    var runMyCode = function($) {
        //define realtime object
        var realtime = {
            refresh: function () {
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
                    data: {id: roomId},
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
            },
        }

        //Init a socket
        var socket = io('http://localhost:3000', {
            'reconnectionAttempts': 3,
        });

        //Joined a room
        var room = "room-" + roomId;
        socket.on('connect', function (data) {
            socket.emit('joined', room);
        });

        //Get new players data when someone joining the room
        socket.on('new-player-connected', realtime.refresh);
    }

    var timer = function() {
        if (window.jQuery && window.jQuery.ui) {
            runMyCode(window.jQuery);
        } else {
            window.setTimeout(timer, 100);
        }
    };
    timer();
})()
