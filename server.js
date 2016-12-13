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
    var roomName;
    socket.on('joined', function (room) {
        roomName = room;
        socket.join(room);
        io.sockets.to(room).emit('new-player-connected');
        var pattern = /-(\d+)/;
        roomId = pattern.exec(roomName);

        //Update states of two players

    });
});