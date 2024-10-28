import pandas as pd
from bs4 import BeautifulSoup
import time, csv
from selenium import webdriver
from selenium.webdriver.chrome.options import Options as ChromeOptions #firefox.options import Options as FirefoxOptions
from selenium.common.exceptions import TimeoutException
data = []
start = time.time()

options = ChromeOptions()
prefs = {"profile.managed_default_content_settings.images": 2, "profile.default_content_setting_values.javascript": 2}
options.add_experimental_option("prefs", prefs)
options.add_extension("src/scrape/adblock.crx")
options.add_argument("--autoplay-policy=no-user-gesture-required")
options.add_argument(f'--proxy-server={None}')
options.add_argument("--no-proxy-server")
options.add_argument("--proxy-server='direct://'")
options.add_argument("--proxy-bypass-list=*")
user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36'
options.add_argument(f'user-agent={user_agent}')
driver = webdriver.Chrome(options=options)

def info_getter(url):
    info = [None] * 9
    try:
        driver.get(url)
        time.sleep(0)
        soup = BeautifulSoup(driver.page_source, 'html.parser')
        syn = soup.find_all("li", class_="c-heroMetadata_item")
        if (len(syn) != 0):
            info[0] = syn[1].text.strip()
        else:
            info[0] = "N/A"

        table = soup.find("div", class_="c-productHero_score-container")
        assert table, "table not found specific " + url

        dev = soup.find("div", "c-gameDetails_Developer")
        info[1] = dev.find("li", "c-gameDetails_listItem").text.strip()
        plat = soup.find("div", "c-gameDetails_Platforms")
        plat = plat.find_all("li", "c-gameDetails_listItem")
        info[2] = [x.text.strip() for x in plat]
        info[3] = soup.find("li", "c-genreList_item").text.strip()
        info[4] = table.find("span", class_="u-text-uppercase").text.strip()
        info[5] = table.find("div", class_="c-siteReviewScore_background-critic_medium").text.strip()
        info[7] = table.find("div", class_="c-siteReviewScore_background-user").text.strip()

        count = 6
        for item in table.find_all("span", class_="c-productScoreInfo_reviewsTotal"):
            info[count] = item.text.strip().split()[2:3][0]
            count += 2
    except TimeoutException:
        pass
    except AttributeError:
        print("Error for " + url + "\n")
        errorFile(url)
        time.sleep(1)
        info[7] = 0.0
        info[6] = table.find("span", class_="c-productScoreInfo_reviewsTotal").text.strip().split()[2:3][0]
        info[8] = 0
    
    return info

def convertForCSV(name, info, url, img):
    data = []
    info = correct(info)
    total = int(''.join(str(info[8]).split(',')))
    data.append({
                "name": name,
                "publisher": info[0],
                "developer": info[1],
                "platform(s)": info[2],
                "genre": info[3],
                "release_date": info[4],
                "metascore": int(info[5]),
                "critic_total": int(info[6]),
                "user_score": info[7],
                "user_total": int(total),
                "average_score": getAverage(info[5], info[7]),
                "link": url,
                "img": img
            })
    return data

def correct(info):
    if (info[4] == "Metascore"):
        info[4] = "Unknown"
    if (str(info[5]) == 'tbd'):
        info[5] = 0
    if (info[6] == None):
        info[6] = 0
    if (str(info[7]) == 'tbd'):
        info[7] = float(0)
    if (info[8] == None):
        info[8] = 0
    return info

def getAverage(meta, user):
    meta = float(meta)
    user = float(user)
    if (meta == 0 and user != 0):
        return user * 10
    elif (meta != 0 and user == 0):
        return meta
    else:
        return (meta + (user * 10)) / 2

def writeToCSV(data):
    with open('csv/inf5.csv', 'a') as file:
        writer = csv.DictWriter(file, fieldnames=['name', "publisher", "developer", "platform(s)", "genre", "release_date", "metascore", "critic_total", "user_score", "user_total", "average_score", 'link', 'img'])
        writer.writerow(data)
        file.close()

def errorFile(data):
    f = open("src/scrape/error/error.txt", "a")
    f.write(data + "\n")
    f.close()

def fix():
    with open('csv/inf5.csv', newline='') as in_file:
            with open('csv/inf5-1.csv', 'w', newline='') as out_file:
                writer = csv.writer(out_file)
                for row in csv.reader(in_file):
                    if row:
                        writer.writerow(row)
"""def other(url, info):
    try:
        driver.get(url)
        time.sleep(1)
        driver.execute_script("window.scrollBy(0,3350)")  
        soup = BeautifulSoup(driver.page_source, 'html.parser')

        dev = soup.find("div", "c-gameDetails_Developer")
        try:
            info[0]['developer'] = dev.find("li", "c-gameDetails_listItem").text.strip()
        except AttributeError:
            info[0]['developer'] = "needs_fixing"
        plat = soup.find("div", "c-gameDetails_Platforms")
        plat = plat.find_all("li", "c-gameDetails_listItem")
        info[0]['platform(s)'] = [x.text.strip() for x in plat]
        info[0]['genre'] = soup.find("li", "c-genreList_item").text.strip()
    except TimeoutException:
        print("error at " + url)
        errorFile(url)
        info = other(url, info)
    return info"""

if __name__ == '__main__':
    tables = pd.read_csv("csv/newinfo.csv")
    names = tables['name']
    links = tables['link']
    images = tables['img']
    data = []
    start_time = time.time()
    game = 0

    for url in links:
        info = info_getter(url)
        data = convertForCSV(names[game], info, url, images[game])
        writeToCSV(data.pop(0))
        game+=1
    fix()
    print("--- %s seconds ---" % (time.time() - start_time))
