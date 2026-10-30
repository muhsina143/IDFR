const { spawn } = require('child_process');
const http = require('http');

async function main() {
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const tempProfile = 'C:\\Users\\muhsi\\Downloads\\IDFR_LATEST\\scratch\\chrome_profile_verify';

    console.log('Launching headless Chrome for comprehensive verification...');
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
        const consoleErrors = [];

        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.id && pending.has(msg.id)) {
                const resolve = pending.get(msg.id);
                pending.delete(msg.id);
                resolve(msg.result);
            }
            if (msg.method === 'Runtime.exceptionThrown') {
                const desc = msg.params.exceptionDetails.exception?.description || msg.params.exceptionDetails.text;
                exceptions.push(desc);
                console.error('[EXCEPTION]', desc);
            }
            if (msg.method === 'Runtime.consoleAPICalled' && msg.params.type === 'error') {
                const text = msg.params.args.map((a) => a.value || a.description).join(' ');
                consoleErrors.push(text);
                console.error('[CONSOLE ERROR]', text);
            }
        };

        await send('Runtime.enable');
        await send('Page.enable');

        console.log('\n--- 1. Testing Homepage (http://127.0.0.1:8000/) ---');
        await send('Page.navigate', { url: 'http://127.0.0.1:8000/' });
        await new Promise((r) => setTimeout(r, 3500));

        const homeState = (await send('Runtime.evaluate', {
            expression: `({
                isLoaded: document.body.classList.contains('is-loaded'),
                heroSlidesCount: document.querySelectorAll('#heroStage .hero-slide').length,
                hasActiveSlide: !!document.querySelector('#heroStage .hero-slide.is-active'),
                modelCardsCount: document.querySelectorAll('#modelGrid .model-card').length,
                creativeProsCount: document.querySelectorAll('#creative-professionals .creative-card').length,
                seasonTitle: document.querySelector('.hero__title-a')?.textContent + ' ' + (document.querySelector('.hero__title-b')?.textContent || ''),
                heroTagline: document.querySelector('.hero__tagline')?.textContent?.trim()
            })`,
            returnByValue: true
        }))?.result?.value;

        console.log('Homepage State:', homeState);

        // Test scrolling on Homepage
        console.log('\nTesting scrolling on Homepage...');
        const scrollResult = (await send('Runtime.evaluate', {
            expression: `(function() {
                window.scrollTo(0, 1200);
                return { scrollY: window.scrollY || document.documentElement.scrollTop };
            })()`,
            returnByValue: true
        }))?.result?.value;
        console.log('Scroll Result:', scrollResult);

        // Test Model Filtering on Homepage
        console.log('\nTesting model filter interaction...');
        const filterResult = (await send('Runtime.evaluate', {
            expression: `(function() {
                const btn = document.querySelector('[data-filter="runway"]');
                if (btn) btn.click();
                return {
                    clickedRunway: !!btn,
                    activeFilter: document.querySelector('.filter-btn.is-active')?.dataset.filter
                };
            })()`,
            returnByValue: true
        }))?.result?.value;
        await new Promise((r) => setTimeout(r, 500));
        const filteredCount = (await send('Runtime.evaluate', {
            expression: `document.querySelectorAll('#modelGrid .model-card').length`,
            returnByValue: true
        }))?.result?.value;
        console.log('Filter Result:', filterResult, 'Visible models under runway filter:', filteredCount);

        // Test opening a model profile modal
        console.log('\nTesting opening Model Profile Modal...');
        const modalOpenResult = (await send('Runtime.evaluate', {
            expression: `(function() {
                const quickBtn = document.querySelector('[data-model-open]');
                if (!quickBtn) return { error: 'No model open button found' };
                quickBtn.click();
                const modal = document.querySelector('#modelModal');
                return {
                    clickedId: quickBtn.dataset.modelOpen,
                    modalIsOpen: modal?.classList.contains('is-open') || modal?.getAttribute('aria-hidden') === 'false',
                    modalName: document.querySelector('#modelModalName')?.textContent
                };
            })()`,
            returnByValue: true
        }))?.result?.value;
        console.log('Model Modal Result:', modalOpenResult);

        console.log('\n--- 2. Testing About Page (http://127.0.0.1:8000/about) ---');
        await send('Page.navigate', { url: 'http://127.0.0.1:8000/about' });
        await new Promise((r) => setTimeout(r, 3500));

        const aboutState = (await send('Runtime.evaluate', {
            expression: `({
                isLoaded: document.body.classList.contains('is-loaded'),
                title: document.title,
                hasMain: !!document.querySelector('main')
            })`,
            returnByValue: true
        }))?.result?.value;
        console.log('About Page State:', aboutState);

        console.log('\n--- 3. Testing Models Page (http://127.0.0.1:8000/models) ---');
        await send('Page.navigate', { url: 'http://127.0.0.1:8000/models' });
        await new Promise((r) => setTimeout(r, 3500));

        const modelsPageState = (await send('Runtime.evaluate', {
            expression: `({
                isLoaded: document.body.classList.contains('is-loaded'),
                modelCardsCount: document.querySelectorAll('#modelGrid .model-card').length
            })`,
            returnByValue: true
        }))?.result?.value;
        console.log('Models Page State:', modelsPageState);

        console.log('\n=============================================');
        console.log('VERIFICATION SUMMARY:');
        console.log(`Total Runtime Exceptions: ${exceptions.length}`);
        console.log(`Total Console Errors: ${consoleErrors.length}`);
        console.log('Homepage Preloader Dismissed:', homeState.isLoaded);
        console.log('Hero Slides Rendered:', homeState.heroSlidesCount);
        console.log('Homepage Models Rendered:', homeState.modelCardsCount);
        console.log('About Page Preloader Dismissed:', aboutState.isLoaded);
        console.log('Models Page Models Rendered:', modelsPageState.modelCardsCount);
        console.log('=============================================');

        if (exceptions.length === 0 && homeState.isLoaded && homeState.heroSlidesCount > 0 && homeState.modelCardsCount > 0) {
            console.log('ALL TESTS PASSED SUCCESSFULLY!');
        } else {
            console.error('VERIFICATION FAILED!');
            process.exit(1);
        }

        ws.close();
    } finally {
        chromeProc.kill('SIGKILL');
    }
}

main().catch((err) => {
    console.error('Fatal Test error:', err);
    process.exit(1);
});

