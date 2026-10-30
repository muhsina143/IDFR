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

        const exceptions = [];

        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.id && pending.has(msg.id)) {
                const resolve = pending.get(msg.id);
                pending.delete(msg.id);
                resolve(msg.result);
            }
            if (msg.method === 'Runtime.exceptionThrown') {
                exceptions.push(msg.params.exceptionDetails);
                console.log('\n>>> EXCEPTION CAUGHT <<<');
                console.log(msg.params.exceptionDetails.exception?.description);
            }
        };

        await send('Runtime.enable');
        await send('Page.enable');
        await send('Page.navigate', { url: 'http://127.0.0.1:8000/' });

        await new Promise((r) => setTimeout(r, 4000));

        const evalResult = await send('Runtime.evaluate', {
            expression: `({
                bodyClasses: document.body.className,
                heroSlidesCount: document.querySelectorAll('#heroStage .hero-slide').length,
                modelCardsCount: document.querySelectorAll('#modelGrid .model-card').length,
                isPreloaderVisible: !document.body.classList.contains('is-loaded')
            })`,
            returnByValue: true
        });

        console.log('\nPage state before fix:', evalResult?.result?.value);
        console.log('Exceptions count:', exceptions.length);
        ws.close();
    } finally {
        chromeProc.kill('SIGKILL');
    }
}

main().catch((err) => {
    console.error('Test error:', err);
    process.exit(1);
});
