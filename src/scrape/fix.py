import pandas as pd
from datetime import datetime
import csv

def writeToCSV(data):
    with open('csv/inf2-final.csv', 'a') as file:
        writer = csv.DictWriter(file, fieldnames=['id', 'name', "publisher", "developer", "platform(s)", "genre", "release_date", "metascore", "critic_total", "user_score", "user_total",  "average_score", 'link', 'img'])
        writer.writerow(data)
        file.close()

def correct():
    with open('csv/inf2-final.csv', newline='') as in_file:
            with open('csv/inf2-finale.csv', 'w', newline='') as out_file:
                writer = csv.writer(out_file)
                for row in csv.reader(in_file):
                    if row:
                        writer.writerow(row)

def main():
    tables = pd.read_csv('csv/inf2-1.csv')
    names = tables['name']
    publishers = tables['publisher']
    developers = tables['developer']
    genres = tables['genre']
    platforms = tables['platform(s)']
    release_dates = tables['release_date'].replace('Unknown', 'Dec 31 3000')
    metascores = tables['metascore'].fillna(0.0)
    critictotal = tables['critic_total'].fillna(0)
    userscores = tables['user_score'].fillna(0.0)
    user_total = tables['user_total'].fillna(0)
    links = tables['link']
    images = tables['img']
    data = []

    #Sorts the data by the release date of each game
    dates = release_dates.tolist()
    dates = [str(s).replace(',','') for s in dates]
    release_dates = dates[:]
    dates.sort(key = lambda date: datetime.strptime(date, '%b %d %Y'))
    id = 1
    lst = links.tolist()
    for i in range(len(tables)):
        if (float(metascores[i]) == 0 and float(userscores[i]) != 0):
            average = float(userscores[i]) * 10
        elif (float(metascores[i]) != 0 and float(userscores[i]) == 0):
            average = float(metascores[i])
        else:
            average = (float(metascores[i]) + ((float(userscores[i]) * 10))) / 2
        data.append({
                "id": id,
                "name": names[i],
                "publisher": publishers[i],
                "developer": developers[i],
                "platform(s)": platforms[i],
                "genre": genres[i],
                "release_date": release_dates[i],
                "metascore": metascores[i],
                "critic_total": int(critictotal[i]),
                "user_score": userscores[i],
                "user_total": int(user_total[i]),
                "average_score": average,
                "link": links[i],
                "img": images[i]
            })
        id += 1
        writeToCSV(data.pop(0))
    correct()
main()