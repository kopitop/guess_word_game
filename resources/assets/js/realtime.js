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
                $('#word').html('Please describe the word, you have been given, by your creativity: ' + data.word.content);
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
            },
            renderImage: function (data) {
                $('#play-panel').html(
                    '<img id="image" src="'
                        + imagePath + '/' + data.current_round.image +
                    '">\
                    <h3>' + drawerWaiting + '</h3>\
                    <h3 id="result"></h3>'
                );
            },
        }

        //define guesser object
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
            },
            renderImage: function (data) {
                $('#play-panel').html(
                    '<img id="image" src="'
                        + imagePath + '/' + data.current_round.image +
                    '">\
                    <input id="answer" type="text" class="form-control" placeholder="'
                        + placeholderAnswer +
                    '"><h3 id="result"></h3>\
                    <a id="submit-answer" href="javascript:;" class="pull-right btn btn-success">'
                        + sendButton +
                    '</a>'
                );
            },
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

        //Quit button
        $(document).on('click','#quit-button', function () {
            var url = laroute.route('rooms.quit');

            $.post(url, {id: roomId}, function (response) {
                if (response.status == 200) {
                    socket.emit('quit');
                    window.location.replace(laroute.route('rooms.index'));
                } else {
                    showError();
                }
            });
        });

        //Get new players data when a player quiting the room
        socket.on('a-player-quit', eval(userRole + '.refresh'));

        //ajax send message info to controller
        $('#target').submit(function( event ) {
            event.preventDefault();
            var url = laroute.route('users.chat');
            var chatContent = $('#content-chat').val();
            $('#content-chat').val('');
                $.ajax({
                    method: 'POST',
                    url: url,
                    data: {room_id: roomId, content: chatContent},
                    dataType: 'json',
                    success: function (data) {
                        data.name = userName;
                        data.avatar = avatarUser;
                        socket.emit('user-send-message', data);
                    }
                });
            });

            //Convert to html
            var encodeHtmlEntity = function(str) {
                var buf = [];
                for (var i = str.length - 1; i >= 0; i--) {
                    buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
                }
                return buf.join('');
            };

            //append new message to chat box
            socket.on('get-new-message', function(data) {
                var chat = $("#chat");
                chat.append (
                    '<li class="' + (authUserID == data.sender_id ?  'right' : 'left')+ ' clearfix">\
                        <span class="chat-img pull-right">\
                            <img src=" '+data.avatar+' " class="chat-icon"/>\
                        </span>\
                        <div class="chat-body clearfix">\
                            <div class="header">\
                                <small class=" text-muted chat-time"><span class="glyphicon"></span></small>\
                                    <strong class="pull-right primary-font chat-name">'+data.name+'</strong>\
                            </div>\
                            <p class="pull-right chat-content">'
                                +encodeHtmlEntity(data.content)+
                            '</p>\
                        </div>\
                    </li>'
                ).parent().animate({ scrollTop: chat.height() }, 300); // scroll to bottom after load new message
            });

        //When the drawer click send image
        $(document).on('click','#send-image', function () {
            var url = laroute.route('rooms.post-image');
            $.post(url, {id: roomId, image:$('#wPaint').wPaint('image')}, function (response) {
                if (response.status == 200) {
                    socket.emit('image-sent', response.data);
                } else {
                    showError();
                }
            });
        });

        //Rendering new play panel when the drawer 's sent image
        socket.on('render-image', eval(userRole + '.renderImage'));

        //Submit answer
        $(document).on('click', '#submit-answer', function (){
            var url = laroute.route('rooms.post-answer');
            $.post(url, {id: roomId, answer: $('#answer').val()}, function (response) {
                if (response.status == 200) {
                    socket.emit('answer-sent', response.data);
                } else {
                    showError();
                }
            });
        });

        //Reload to update result after guesser's sent answer 
        socket.on('render-result', function () {
            location.reload();
        });

        //New round
        $(document).on('click', '#new-round', function () {
            var url = laroute.route('rooms.new-round');
            $.post(url, {id: roomId}, function (response) {
                if (response.status == 200) {
                    socket.emit('new-round');
                } else {
                    showError();
                }
            });
        });

        //Get new round
        socket.on('get-new-round', function () {
            location.reload();
        });
        
        //When a player click finish, we'll close the room
        $(document).on('click', '#finish-button', function () {
            var url = laroute.route('rooms.finish');
            $.post(url, {id: roomId}, function (response) {
                if (response.status == 200) {
                    socket.emit('finish');
                } else {
                    showError();
                }
            });
        });

        //After closing room, we need to refresh page of players
        socket.on('close-room', function () {
            location.reload();
        });
    }

    var timer = function() {
        if (window.jQuery && window.jQuery.ui && typeof roomStatus != 'undefined') {
            runMyCode(window.jQuery);
        } else {
            window.setTimeout(timer, 100);
        }
    };
    timer();
})()
