from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.common.exceptions import TimeoutException
import time, csv, os

base_url = "https://www.metacritic.com"
main_url = "https://www.metacritic.com/browse/game/?releaseYearMin=1958&releaseYearMax=2024&page="
pages = 557

def writeToCSV(data):
    file_exists = os.path.isfile('csv/newinfo.csv')
    with open('csv/newinfo.csv', 'a') as file:
        writer = csv.DictWriter(file, fieldnames=['name', 'link','img'])
        if not file_exists:
            writer.writeheader()
        writer.writerow(data)
        file.close()

def main():
    driver = webdriver.Firefox()
    images = []
    data = []
    
    start_time = time.time()
    for i in range(1, pages+1):
        url = main_url + str(i)
        try:
            driver.get(url)
            time.sleep(2)
            driver.execute_script("window.scrollBy(0,500)")
            time.sleep(1)
            driver.execute_script("window.scrollBy(0,500)")
            time.sleep(2)
            driver.execute_script("window.scrollBy(0,500)")
        except TimeoutException:
            pass
        soup = BeautifulSoup(driver.page_source, 'html.parser')
        table = soup.find("section", class_="c-pageBrowse_content")
        assert table, "table not found"
        count = 0
        
        for item in table.find_all("div", class_="c-finderProductCard"):
            name = item.find("h3", class_="c-finderProductCard_titleHeading")
            link = item.find('a', href=True)
            img = item.find('div', class_="c-finderProductCard_img")
            img = img.find('img')
            if img is None:
                images.append('https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg')
            else:
                images.append(img.get('src'))   
            page_url = base_url + str(link['href'])
            data.append({
                "name": name.text.strip().split(None, 1)[-1],
                "link": page_url,
                "img": images.pop(0)
            })
            writeToCSV(data.pop(0))
    print("--- %s seconds ---" % (time.time() - start_time))
main()
