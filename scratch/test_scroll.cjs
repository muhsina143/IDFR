const { spawn } = require('child_process');
const http = require('http');

async function testScroll() {
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const tempProfile = 'C:\\Users\\muhsi\\Downloads\\IDFR_LATEST\\scratch\\chrome_profile_scroll';

    const chromeProc = spawn(chromePath, [
        '--headless=new',
        '--remote-debugging-port=9222',
        `--user-data-dir=${tempProfile}`,
        '--window-size=1440,900',
        'about:blank'
    ]);

    await new Promise((r) => setTimeout(r, 2000));

    try {
        const pages = await new Promise((resolve) => {
            http.get('http://127.0.0.1:9222/json/list', (res) => {
                let data = '';
                res.on('data', (c) => (data += c));
                res.on('end', () => resolve(JSON.parse(data)));
            });
        });

        const targetPage = pages.find((p) => p.type === 'page') || pages[0];
        const ws = new WebSocket(targetPage.webSocketDebuggerUrl);
        await new Promise((resolve) => (ws.onopen = resolve));

        let id = 1;
        const pending = new Map();
        const send = (method, params = {}) => {
            const reqId = id++;
            return new Promise((resolve) => {
                pending.set(reqId, resolve);
                ws.send(JSON.stringify({ id: reqId, method, params }));
            });
        };

        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.id && pending.has(msg.id)) {
                const resolve = pending.get(msg.id);
                pending.delete(msg.id);
                resolve(msg.result);
            }
        };

        await send('Runtime.enable');
        await send('Page.enable');
        await send('Page.navigate', { url: 'http://127.0.0.1:8000/' });
        await new Promise((r) => setTimeout(r, 3500));

        const scrollInfo = (await send('Runtime.evaluate', {
            expression: `(function() {
                window.scrollTo({ top: 1200, behavior: 'instant' });
                return {
                    scrollYInstant: window.scrollY || document.documentElement.scrollTop
                };
            })()`,
            returnByValue: true
        }))?.result?.value;

        console.log('Scroll Info Instant:', scrollInfo);
        ws.close();
    } finally {
        chromeProc.kill('SIGKILL');
    }
}

testScroll();
