var app = require('http').createServer(handler)
var io = require('socket.io')(app);
var fs = require('fs');

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
    socket.on('joined', function (room) {
        socket.join(room);
        io.sockets.to(room).emit('new-player-connected');
    })
    socket.on('quit', function (room) {
      io.sockets.to(room).emit('a-player-quit');
    })
    socket.on('ready', function (ready, room, userid) {
      io.sockets.to(room).emit('a-player-click-ready',ready ,userid);
    })
});
