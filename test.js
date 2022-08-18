import pkg from 'express';
import fs from 'fs';
const app = new pkg();
import path from 'path';
import { fileURLToPath } from 'url';
import { Server } from 'socket.io';
import http from 'http';
import ejs from 'ejs';
import { match } from 'assert';
const server = http.createServer(app);
const io = new Server(server);

const port = 91;

const __filename = fileURLToPath(import.meta.url);

const __dirname = path.dirname(__filename);

app.set('view engine', 'ejs');
app.set('views', './');
app.use(pkg.static(__dirname + '/public'));

app.get('/', function (req, res) {
    res.render('test', { data: 'Express' });
});

io.on('connection', function (socket) {
    console.log('connection socket');

    socket.on('on-chat', function (message) {
        console.log(message.type);
    });
});

// send data to socket example
setInterval(function () {
    io.emit('message', { type: Math.random() });
}, 5000);

server.listen(port, () => {
    console.log(`Example app listening on port ${port}`);
});
