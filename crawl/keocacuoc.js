import { Builder, Browser, By, Key, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome';
import cheerio from 'cheerio';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);

const __dirname = path.dirname(__filename);

import config from './keocacuocconfig';

const options = new chrome.Options();
options.addArguments('headless');
options.addArguments('disable-gpu');
options.addArguments('no-sandbox');

options.addArguments(
    '--user-agent=Mozilla/5.0 (iPhone; CPU iPhone OS 10_3 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) CriOS/56.0.2924.75 Mobile/14E5239e Safari/602.1'
);

(async function crawl() {
    let driver = await new Builder().forBrowser(Browser.CHROME).setChromeOptions(options).build();
    try {
        await driver.get(config.crawlURL);

        const page = await driver.findElement(By.css('html')).getAttribute('innerHTML');
        const $ = cheerio.load(page);

        const title = 'pc' + 1 + '.txt';
        let data = $('#keo_main');
        data.find('.wordad').remove();
        data = data.prop('outerHTML');

        getData(data, title);

        await driver.findElement(By.css('#keosom')).click();

        await driver.sleep(3 * 1000);

        for (let i = 2; i <= 8; i++) {
            if (i !== 2) {
                const button = '.c-odds-page__filter .nofil:nth-child(' + i + ')';
                await driver.findElement(By.css(button)).click();
                await driver.sleep(2 * 1000);
            }

            const page = await driver.findElement(By.css('html')).getAttribute('innerHTML');
            const $ = cheerio.load(page);

            const title = 'pc' + i + '.txt';
            let data = $('#transom');
            data.find('.wordad').remove();
            data = data.prop('outerHTML');

            getData(data, title);
        }

        await driver.quit();
    } finally {
        await driver.quit();
    }
})();

async function getData(data, title) {
    saveFile('keocacuoc/', title, data);
}

function saveFile(pathFolderSave, fileName, data) {
    if (fileName) {
        const path = __dirname + '/' + pathFolderSave + fileName;

        try {
            fs.writeFileSync(path, data, '');

            console.log('Ghi file ' + path);
        } catch {
            console.log('Lỗi ghi file' + path);
        }
    }
}
