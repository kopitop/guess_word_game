(function() {
    var runMyCode = function($) {
        //Inject csrf-token to ajax request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //define drawer object
        var drawer = {
            refresh: function () {
                var url = laroute.route('rooms.refresh');

                $.post(url, {id: roomId}, function (response) {
                        if ( response.status  == 200 ) {
                            refreshSuccess (response.data);
                        } else {
                            showError();
                        }
                    }
                );
            },
            beginPlay: function (data) {
                $('#word').html(data.word.content);
                $('.is-ready').html('<button class="btn btn-success btn-sm pull-right">' + playingButton + '</button>');
                $('#ready-button').remove();
                $('#quit-button').remove();
                $('.action-room .form-group').append(
                    '<a id="finish-button" class="btn btn-warning" href="javascript:;">'
                        + finishButton +
                    '</a>\
                ')
                $('#play-panel').html(
                    '<div id="wPaint"></div>\
                    <h3 id="result"></h3>\
                    <a id="send-image" href="javascript:;" class="pull-right btn btn-success">'
                        + sendButton +    
                    '</a>\
                ');
                $('#wPaint').wPaint();
            }
        }

        //define drawer object
        var guesser = {
            refresh: function () {
                var url = laroute.route('rooms.refresh');

                $.post(url, {id: roomId}, function (response) {
                        if ( response.status  == 200 ) {
                            refreshSuccess (response.data);
                        } else {
                            showError();
                        }
                    }
                );
            },
            beginPlay: function (data) {
                $('.is-ready').html('<button class="btn btn-success btn-sm pull-right">' + playingButton + '</button>');
                $('#ready-button').remove();
                $('#quit-button').remove();
                $('.action-room .form-group').append(
                    '<a id="finish-button" class="btn btn-warning" href="javascript:;">'
                        + finishButton +
                    '</a>\
                ')
                $('#play-panel').html(
                    '<h3 class="waiting">'
                        + guesserWaiting +
                    '</h3>\
                    <h3 id="result"></h3>'
                );

            }
        }

        //Define common functions
        function refreshSuccess (data) {
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
            if (data.room.status != playingStatus) {
                $('.is-ready').html('');
            }
            if (data.room.status == fullStatus) {
                $('.ready-block').html(
                    '<a id="ready-button" class="btn btn-success" href="javascript:;">'
                    + readyButton +
                    '</a>\
                    <input type="hidden" name="ready" id="ready-status" value="0">'
                );
            }
        }

        function showError() {
            $('.ajax-error').show(400).html(errorMessage);
            
            setTimeout(function () {
                $('.ajax-error').hide(400);
            }, 3000);
        }

        //Init a socket
        var socket = io('http://localhost:3000', {
            'reconnectionAttempts': 3,
        });

        //Joined a room
        socket.on('connect', function (data) {
            socket.emit('joined', roomId);
        });

        //Get new players data when someone joining the room
        socket.on('new-player-connected', eval(userRole + '.refresh'));

        //When a user click ready, we'll update state of the room in the database and info panel
        $(document).on('click','#ready-button', function () {
            $('#ready-status').val(($('#ready-status').val() == 1) ? 0 : 1);
            var url = laroute.route('rooms.ready');

            $.post(url, {id: roomId, ready: parseInt($('#ready-status').val())}, function (response) {
                if (response.status == 200) {
                    socket.emit('ready', parseInt($('#ready-status').val()), userId);

                    //And if both of players 'r ready, we'll start the game
                    if (response.state == 15) {
                        socket.emit('all-ready');
                    }
                } else {
                    showError();
                }
            });
        });

        //Updating state of players on the info panel
        socket.on('update-state', function (ready, userId) {
            if ($('.drawer').data('userid') == userId && ready == 1) {
                $('.drawer .is-ready')
                    .append('<a href="javascript:;" class="btn btn-info btn-sm pull-right">' + readyButton + '</a>');
            } else if ($('.drawer').data('userid') == userId && ready == 0) {
                $('.drawer .is-ready').text('');
            }

            if ($('.guesser').data('userid') == userId && ready == 1) {
                $('.guesser .is-ready')
                    .append('<a href="javascript:;" class="btn btn-info btn-sm pull-right">' + readyButton + '</a>');
            } else if ($('.guesser').data('userid') == userId && ready == 0) {
                $('.guesser .is-ready').text('');
            }
        })

        //Start the game
        socket.on('begin-play', function (response) {
            if (response.status == 200) {
                eval(userRole + '.beginPlay(response.data)');
            } else {
                showError();
            }
        });
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
