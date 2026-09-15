async (page) => {
  const session = await page.context().newCDPSession(page);
  await session.send('Network.enable');
  await session.send('Network.setCacheDisabled', {cacheDisabled: true});
  const report = { routes: [], errors: [], externalRequests: [] };
  page.on('pageerror', error => report.errors.push(error.message));
  page.on('request', request => {
    if (!request.url().startsWith('http://127.0.0.1:18201/')) report.externalRequests.push(request.url());
  });
  const routes = ['/', '/product/', '/point-of-sale/', '/download/', '/support/', '/roadmap/', '/news/', '/news/0-1-0-preview/', '/credits/'];
  for (const width of [1440, 768, 320]) {
    await page.setViewportSize({width, height: 1000});
    for (const route of routes) {
      const response = await page.goto('http://127.0.0.1:18201' + route);
      await page.evaluate(async () => { await document.fonts.ready; });
      // Trigger lazy images without asserting that offscreen lazy requests finish at page load.
      await page.evaluate(async () => {
        for (const image of document.images) {
          if (!image.getAttribute('src')) continue;
          image.loading = 'eager';
          try { await image.decode(); } catch {}
        }
      });
      const state = await page.evaluate(() => ({
        width: innerWidth, scrollWidth: document.documentElement.scrollWidth,
        brokenImages: [...document.images].filter(i => i.getAttribute('src') && !i.naturalWidth).map(i => i.getAttribute('src')),
        currentNav: [...document.querySelectorAll('nav [aria-current=page]')].map(a => a.textContent.trim()),
      }));
      report.routes.push({route, viewport: width, status: response.status(), ...state});
      if (state.scrollWidth > state.width || state.brokenImages.length || response.status() !== 200) report.errors.push(route + ' at ' + width + ' failed');
      if ((width === 1440 && route === '/') || (width === 768 && route === '/product/') || (width === 320 && ['/support/', '/credits/'].includes(route))) {
        await page.screenshot({path: 'docs/design/website/qa/redesign-' + (route === '/' ? 'home' : route.split('/')[1]) + '-' + width + '.png', fullPage: false});
      }
    }
  }
  const missing = await page.goto('http://127.0.0.1:18201/does-not-exist/');
  report.notFound = {status: missing.status(), heading: await page.locator('h1').innerText()};
  for (const route of ['/credits.html', '/index.html', '/robots.txt', '/sitemap.xml', '/llms.txt', '/news/feed.xml']) {
    const response = await page.request.get('http://127.0.0.1:18201' + route, {maxRedirects:0});
    report.routes.push({route, status:response.status(), location:response.headers().location, contentType:response.headers()['content-type'], csp:!!response.headers()['content-security-policy']});
  }
  await session.detach();
  return report;
}
