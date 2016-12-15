var app = require('http').createServer(handler)
var io = require('socket.io')(app);
var fs = require('fs');
var request = require('request');

app.listen(3000);

function handler (req, res) {
    fs.readFile(__dirname + '/index.html',
    function (err, data) {
        if (err) {
        res.writeHead(500);
        return res.end('Error loading index.html');
        }

        res.writeHead(200);
        res.end(data);
    });
}

io.sockets.on('connection', function(socket) {
    var roomId;

    socket.on('joined', function (room) {
        roomId = room;
        socket.join(room);
        io.sockets.to(room).emit('new-player-connected');
    });

    //If a user disconnect (quit, internet, close browser,..) when in the waiting room, we 'll reset ready state
    socket.on('disconnect', function () {
        option = {
            uri: 'http://gwg.app/rooms/reset-state/' + roomId,
            method: "GET",
        }
        request(option);
    });

    //When a player click ready, we'll refresh ready state on room's info panel
    socket.on('ready', function (ready, userId) {
        io.sockets.to(roomId).emit('update-state',ready ,userId);
    });

    //When both players ready, we'll start the game
    socket.on('all-ready', function () {
        io.sockets.to(roomId).emit('start-to-play');
    });
});
