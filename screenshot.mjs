import puppeteer from 'puppeteer';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

(async () => {
    const browser = await puppeteer.launch({
        headless: "new"
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 1080 });
    await page.goto('http://e-commerce.test/cart', { waitUntil: 'networkidle0' });
    
    await page.screenshot({ path: '/Users/agustokenyjia/.gemini/antigravity-ide/brain/bc045100-10cb-4869-854a-15bb6307334f/cart_debug.png' });
    
    await browser.close();
    console.log('Screenshot saved to cart_debug.png');
})();
