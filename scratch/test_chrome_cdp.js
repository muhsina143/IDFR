const { spawn } = require('child_process');
const http = require('http');

async function main() {
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const tempProfile = 'C:\\Users\\muhsi\\Downloads\\IDFR_LATEST\\scratch\\chrome_profile';

    console.log('Launching headless Chrome...');
    const chromeProc = spawn(chromePath, [
        '--headless=new',
        '--remote-debugging-port=9222',
        `--user-data-dir=${tempProfile}`,
        '--disable-gpu',
        '--no-first-run',
        '--no-default-browser-check',
        'about:blank'
    ]);

    await new Promise((r) => setTimeout(r, 2000));

    try {
        const pages = await new Promise((resolve, reject) => {
            http.get('http://127.0.0.1:9222/json/list', (res) => {
                let data = '';
                res.on('data', (c) => (data += c));
                res.on('end', () => resolve(JSON.parse(data)));
            }).on('error', reject);
        });

        console.log('Attached to Chrome page:', pages[0]?.title);
        const wsUrl = pages[0]?.webSocketDebuggerUrl;
        if (!wsUrl) throw new Error('No debugger url');

        const ws = new WebSocket(wsUrl);
        await new Promise((resolve) => ws.onopen = resolve);

        let id = 1;
        const send = (method, params = {}) => {
            const reqId = id++;
            ws.send(JSON.stringify({ id: reqId, method, params }));
            return reqId;
        };

        const exceptions = [];
        const logs = [];

        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.method === 'Runtime.exceptionThrown') {
                exceptions.push(msg.params.exceptionDetails);
                console.log('[BROWSER EXCEPTION]', msg.params.exceptionDetails.text, msg.params.exceptionDetails.exception?.description);
            }
            if (msg.method === 'Runtime.consoleAPICalled') {
                console.log('[BROWSER CONSOLE]', msg.params.type, msg.params.args.map((a) => a.value || a.description).join(' '));
            }
        };

        send('Runtime.enable');
        send('Page.enable');
        send('Page.navigate', { url: 'http://127.0.0.1:8000/' });

        await new Promise((r) => setTimeout(r, 4000));

        console.log('Total exceptions captured:', exceptions.length);
        ws.close();
    } finally {
        chromeProc.kill('SIGKILL');
    }
}

main().catch((err) => {
    console.error('Test error:', err);
    process.exit(1);
});

