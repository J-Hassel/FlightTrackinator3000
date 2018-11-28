import pymysql

connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM airport")

for line in open("airports.dat", encoding="utf8"):

    iataCode = line.split(',')[4][1:-1]

    if len(iataCode) != 3:
        # there is a comma within the name substring
        name = (line.split(',')[1] + line.split(',')[2])[1:-1]
        iataCode = line.split(',')[5][1:-1]
        city = line.split(',')[3][1:-1]
        country = line.split(',')[4][1:-1]
        latitude = round(float(line.split(',')[7]), 4)
        longitude = round(float(line.split(',')[8]), 4)
    else:
        # everything is normal
        name = line.split(',')[1][1:-1]
        iataCode = line.split(',')[4][1:-1]
        city = line.split(',')[2][1:-1]
        country = line.split(',')[3][1:-1]
        latitude = round(float(line.split(',')[6]), 4)
        longitude = round(float(line.split(',')[7]), 4)

    if iataCode == "" or city == "" or country == "":
        continue        # skipping rows with null values

    sql = "INSERT INTO airport(name, iataCode, city, country, latitude, longitude) VALUES (%s, %s, %s, %s, %s, %s);"
    cursor.execute(sql,(name, iataCode, city, country, latitude, longitude))

    connection.commit()
connection.close()

