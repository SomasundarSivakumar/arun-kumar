const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));
  page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
  
  await page.goto('http://localhost:8000/');
  await page.waitForTimeout(3000);
  
  const heroStyle = await page.evaluate(() => {
    const el = document.getElementById('hero');
    if (!el) return 'NO HERO';
    return {
      bgImage: window.getComputedStyle(el).backgroundImage,
      bgColor: window.getComputedStyle(el).backgroundColor,
      vars: {
        heroBgUrl: window.getComputedStyle(el).getPropertyValue('--hero-bg-url'),
        colorBgBase: window.getComputedStyle(el).getPropertyValue('--color-bg-base'),
      }
    };
  });
  console.log('Hero computed styles:', JSON.stringify(heroStyle, null, 2));

  const beforeStyle = await page.evaluate(() => {
    const el = document.querySelector('#hero');
    if (!el) return 'NO HERO';
    const before = window.getComputedStyle(el, '::before');
    return {
      content: before.content,
      backgroundImage: before.backgroundImage,
      display: before.display,
      position: before.position,
      width: before.width,
      height: before.height,
      zIndex: before.zIndex,
      opacity: before.opacity,
      filter: before.filter,
    };
  });
  console.log('Hero::before computed styles:', JSON.stringify(beforeStyle, null, 2));

  await browser.close();
})();
