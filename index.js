import pkg from 'express';
import fs from 'fs';
const app = new pkg();
import path from 'path';
import { fileURLToPath } from 'url';
import { Server } from 'socket.io';
import http from 'http';
const server = http.createServer(app);

const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST'],
    },
});

app.io = io;

const port = 90;

// IMPORTANT CODE
app.use(function (req, res, next) {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});

const __filename = fileURLToPath(import.meta.url);

const __dirname = path.dirname(__filename);

app.use(pkg.static(__dirname + '/public'));

app.get('/api', function (req, res) {
    try {
        const { type, date, device, socket } = req.query;
        const data = fs.readFileSync(__dirname + '/crawl/' + type + '/' + device + date + '.txt', 'utf8');

        switch (type) {
            case 'keocacuoc':
                res.send(viewCaCuoc(data));

                break;
            case 'keonhacai':
                if (device === 'pc') {
                    res.send(viewNhaCai(data));
                } else if (device === 'mobile') {
                    res.send(viewNhaCaiMobile(data));
                } else {
                    res.status(404).send('Not found');
                }
                break;

            default:
                res.status(404).send('Not found');
                break;
        }
    } catch (error) {
        res.status(404).send('Not found');
    }
});

// send data to socket example
setInterval(function () {
    io.emit('keonhacaipc', {
        data: viewNhaCai(fs.readFileSync(__dirname + '/crawl/keonhacai/pc1.txt', 'utf8')),
    });
    io.emit('keocacuoc', {
        data: viewCaCuoc(fs.readFileSync(__dirname + '/crawl/keocacuoc/pc1.txt', 'utf8')),
    });
    io.emit('keonhacaimobile', {
        data: viewNhaCaiMobile(fs.readFileSync(__dirname + '/crawl/keonhacai/mobile1.txt', 'utf8')),
    });
}, 100000);

server.listen(port, () => {
    console.log(`App listening on port ${port}`);
});

function viewCaCuoc(data) {
    const view = `
    <div data-view="asiaview">
        ${data}
    </div>
    `;
    return view;
}

function viewNhaCai(data) {
    const view = `
    <div id="odd-table" class="table-wrapper">
        <div class="heading_table">
            <table id="odd-header-desktop" width="100%" border="0" cellspacing="1" cellpadding="0" style="display: table;">
                <tbody>
                    <tr>
                        <td rowspan="2" width="9%">Giờ</td>
                        <td rowspan="2" width="27%">Trận Đấu</td>
                        <td colspan="3">Cả Trận</td>
                        <td colspan="3">Hiệp 1</td>
                    </tr>
                        <tr class="trd_TYLETT_1">
                        <td class="Tyleweb_1" width="12%">Tỷ Lệ</td>
                        <td class="Tyleweb_1" width="12%">Tài Xỉu</td>
                        <td class="Tyleweb_1" width="8%">1x2</td>
                        <td class="Tyleweb_1" width="12%">Tỷ Lệ</td>
                        <td class="Tyleweb_1" width="12%">Tài Xỉu</td>
                        <td class="Tyleweb_1" width="8%">1x2</td>
                    </tr>
                </tbody>
            </table>
        </div>
        ${data}
    </div>
    `;

    return view;
}
function viewNhaCaiMobile(data) {
    const view = `
    <div id="odd-table" class="table-wrapper">
        <div class="heading_table">
            <table id="odd-header-mobile" width="100%" border="0" cellspacing="1" cellpadding="0" style="display: table;">
                <tbody>
                    <tr>
                        <td width="12%">Giờ</td>
                        <td width="34%">Trận Đấu</td>
                        <td width="22%">Tỷ Lệ</td>
                        <td width="22%">Tài Xỉu</td>
                        <td width="10%">1x2</td>
                    </tr>
                </tbody>
            </table>
        </div>
        ${data}
    </div>
    `;

    return view;
}
